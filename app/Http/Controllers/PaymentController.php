<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $query = Payment::with(['user', 'reservation'])
            ->when(request('search'), function ($query, $search) {
                $query->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('payment_method'), function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->when(request('start_date'), function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when(request('end_date'), function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            });

        // Statistiques
        $totalAmount = Payment::sum('amount');
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        $completedAmount = Payment::where('status', 'completed')->sum('amount');
        $refundedAmount = Payment::where('status', 'refunded')->sum('amount');

        // Pagination
        $payments = $query->latest()->paginate(10);

        return view('payment.index', compact(
            'payments',
            'totalAmount',
            'pendingAmount',
            'completedAmount',
            'refundedAmount'
        ));
    }

    public function create()
    {
        $reservations = Reservation::with(['property', 'user'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('payment_status', 'pending')
            ->get();

        return view('payment.create', compact('reservations'));
    }

    public function store(PaymentStoreRequest $request)
    {

        try {
            $payment = Payment::create([
                'reservation_id' => $request->reservation_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'status' => $request->status,
                'user_id' => auth()->id(),
                'metadata' => [
                    'sender_name' => auth()->user()->name,
                    'sender_phone' => auth()->user()->phone,
                ]
            ]);

            // Mettre à jour le statut de paiement de la réservation si le paiement est complété
            if ($request->status === 'completed') {
                $payment->reservation->update([
                    'payment_status' => 'paid'
                ]);
            }

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Paiement créé avec succès');
        } catch (\Exception $e) {
            // dump($e);
            return back()->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['reservation.property', 'reservation.user']);
        return view('payment.show', compact('payment'));
    }

    public function edit(Request $request, Payment $payment)
    {
        return view('payment.edit', compact('payment'));
    }

    public function update(PaymentUpdateRequest $request, Payment $payment)
    {
        try {
            $payment->update($request->validated());

            // Mettre à jour le statut de paiement de la réservation
            if ($request->status === 'completed') {
                $payment->reservation->update(['payment_status' => 'paid']);
            } elseif ($request->status === 'refunded') {
                $payment->reservation->update(['payment_status' => 'refunded']);
            }

            return redirect()
                ->route('payments.show', $payment)
                ->with('success', 'Paiement mis à jour avec succès');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du paiement.')
                ->withInput();
        }
    }


    public function destroy(Request $request, Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index');
    }
}
