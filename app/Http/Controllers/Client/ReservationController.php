<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\ReservationCanceled;
use App\Notifications\ReservationStatusUpdated;

class ReservationController extends Controller
{
    /**
     * Affiche la liste des réservations de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['property', 'property.images', 'payments'])
            ->where('user_id', Auth::id());

        // Filtrage par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrage par période
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'upcoming':
                    $query->where('check_in', '>=', now());
                    break;
                case 'past':
                    $query->where('check_out', '<', now());
                    break;
                case 'current':
                    $query->where('check_in', '<=', now())
                        ->where('check_out', '>=', now());
                    break;
            }
        }

        $query->orderBy('check_in', $request->get('sort', 'desc'));

        $reservations = $query->paginate(5);

        $stats = [
            'total' => Reservation::where('user_id', Auth::id())->count(),
            'upcoming' => Reservation::where('user_id', Auth::id())
                ->where('check_in', '>=', now())
                ->count(),
            'total_spent' => Reservation::where('user_id', Auth::id())
                ->where('status', 'confirmed')
                ->sum('total_price'),
        ];
        $properties = Property::whereHas('reservations', function ($q) {
            $q->where('user_id', auth()->id());
        })
            ->paginate(5);
        return view('client.reservations.index', compact('reservations', 'stats', 'properties'));
    }

    /**
     * Affiche les détails d'une réservation.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\View\View
     */
    public function show(Reservation $reservation)
    {
        // Vérifier que l'utilisateur est autorisé à voir cette réservation
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette réservation.');
        }

        $reservation->load([
            'property',
            'property.images',
            'payments',
            'property.user'
        ]);

        $data = [
            'nights' => $reservation->check_in->diffInDays($reservation->check_out),
            'cancellable' => $this->isCancellable($reservation),
            'check_in_time' => Carbon::parse($reservation->check_in)->format('H:i'),
            'check_out_time' => Carbon::parse($reservation->check_out)->format('H:i'),
            'total_paid' => $reservation->payments->where('status', 'completed')->sum('amount'),
        ];

        return view('client.reservations.show', compact('reservation', 'data'));
    }

    /**
     * Annule une réservation.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$this->isCancellable($reservation)) {
            return back()->with('error', 'Cette réservation ne peut plus être annulée.');
        }

        try {
            \DB::beginTransaction();

            $reservation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->input('motif'),
                'cancelled_at' => now(),
            ]);

            $reservation->property?->update([
                'status' => 'available'
            ]);

            $this->handleCancellationRefund($reservation);

            $reservation->property->user->notify(
                new ReservationCanceled($reservation)
            );

            Auth::user()->notify(
                new ReservationStatusUpdated($reservation)
            );

            \DB::commit();

            return redirect()
                ->route('client.reservations.index')
                ->with('success', 'Réservation annulée avec succès.');
        } catch (\Throwable $e) {
            \DB::rollBack();

            return back()->with(
                'error',
                'Erreur lors de l’annulation. Veuillez réessayer.'
            );
        }
    }

    /**
     * Vérifie si une réservation peut être annulée.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return bool
     */
    private function isCancellable(Reservation $reservation): bool
    {
        if ($reservation->status === 'cancelled') {
            return false;
        }


        if ($reservation->check_in <= now()) {
            return false;
        }

        if (now()->diffInHours($reservation->check_in, false) < 48) {
            return false;
        }

        return true;
    }

    /**
     * Gère le remboursement en cas d'annulation.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return void
     */
    private function handleCancellationRefund(Reservation $reservation): void
    {
        $completedPayments = $reservation->payments()->where('status', 'completed')->get();

        foreach ($completedPayments as $payment) {
            $refundAmount = $this->calculateRefundAmount($reservation, $payment->amount);

            if ($refundAmount > 0) {
                $reservation->payments()->create([
                    'amount' => -$refundAmount, // Montant négatif pour indiquer un remboursement
                    'status' => 'pending',
                    'type' => 'refund',
                    'original_payment_id' => $payment->id
                ]);
            }
        }
    }

    /**
     * Calcule le montant du remboursement selon la politique d'annulation.
     *
     * @param  \App\Models\Reservation  $reservation
     * @param  float  $paidAmount
     * @return float
     */
    private function calculateRefundAmount(Reservation $reservation, float $paidAmount): float
    {
        $daysUntilCheckIn = now()->diffInDays($reservation->check_in, false);

        if ($daysUntilCheckIn > 7) {
            return $paidAmount * 0.95;
        }

        if ($daysUntilCheckIn > 3) {
            return $paidAmount * 0.5;
        }

        return 0;
    }

    public function destroy(Reservation $reservation)
    {
        try {
            
            if ($reservation->user_id !== auth()->id()) {
                abort(403, 'Action non autorisée.');
            }

            $reservation->delete();

            return redirect()
                ->route('client.reservations.index')
                ->with('success', 'La réservation a été supprimée avec succès.');
        } catch (\Throwable $e) {
            dd($e->getMessage());

            return back()->with('error', 'Erreur lors de la suppression de la réservation.');
        }
    }
}
