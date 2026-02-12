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

        // Tri des réservations
        $query->orderBy('check_in', $request->get('sort', 'desc'));

        $reservations = $query->paginate(10);

        // Statistiques pour le tableau de bord
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
        ->paginate(10);
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

        // Charger les relations nécessaires
        $reservation->load([
            'property',
            'property.images',
            'payments',
            'property.user' // Propriétaire
        ]);

        // Calculer quelques informations supplémentaires
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
        // Vérifier que l'utilisateur est autorisé à annuler cette réservation
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à annuler cette réservation.');
        }

        // Vérifier si la réservation peut être annulée
        if (!$this->isCancellable($reservation)) {
            return back()->with('error', 'Cette réservation ne peut plus être annulée. Contactez le support pour plus d\'informations.');
        }

        try {
            // Début de la transaction
            \DB::beginTransaction();

            // Mettre à jour le statut de la réservation
            $reservation->update([
                'statut' => 'annulee',
                'motif_annulation' => $request->input('motif'),
                'date_annulation' => now()
            ]);

            // Remettre à jour la disponibilité de la propriété si nécessaire
            $property = $reservation->property;
            $property->update(['statut' => 'disponible']);

            // Gérer les remboursements si nécessaire
            $this->handleCancellationRefund($reservation);

            // Notifier le propriétaire
            $reservation->property->user->notify(new ReservationCanceled($reservation));

            // Notifier le locataire
            Auth::user()->notify(new ReservationStatusUpdated($reservation));

            \DB::commit();

            return redirect()
                ->route('client.reservations.index')
                ->with('success', 'Votre réservation a été annulée avec succès. Un email de confirmation vous a été envoyé.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'annulation. Veuillez réessayer.');
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
        // Vérifier si la réservation n'est pas déjà annulée
        if ($reservation->status === 'cancelled') {
            return false;
        }

        // Vérifier si la date de début est dans plus de 48h
        if ($reservation->check_in->diffInHours(now()) < 48) {
            return false;
        }

        // Vérifier si la réservation n'a pas déjà commencé
        if ($reservation->check_in <= now()) {
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
            // Calculer le montant du remboursement selon la politique d'annulation
            $refundAmount = $this->calculateRefundAmount($reservation, $payment->amount);

            if ($refundAmount > 0) {
                // Créer un enregistrement de remboursement
                $reservation->payments()->create([
                    'amount' => -$refundAmount, // Montant négatif pour indiquer un remboursement
                    'status' => 'pending',
                    'type' => 'refund',
                    'original_payment_id' => $payment->id
                ]);

                // Ici, vous pourriez ajouter la logique pour traiter le remboursement via votre système de paiement
                // Par exemple, avec Stripe :
                // \Stripe\Refund::create(['payment_intent' => $payment->payment_intent_id]);
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
        $daysUntilCheckIn = $reservation->check_in->diffInDays(now());

        // Politique de remboursement
        if ($daysUntilCheckIn > 7) {
            // Remboursement total moins les frais de service
            return $paidAmount * 0.95;
        } elseif ($daysUntilCheckIn > 3) {
            // Remboursement de 50%
            return $paidAmount * 0.5;
        } else {
            // Pas de remboursement
            return 0;
        }
    }
}
