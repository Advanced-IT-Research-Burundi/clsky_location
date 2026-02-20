{{-- @component('mail::message') --}}
# Paiement confirmé

Bonjour {{ $payment->user->name }},

Votre paiement pour la réservation **#{{ $payment->reservation->name }}** a été **confirmé**.

**Détails du paiement :**
- Montant : {{ number_format($payment->amount, 2) }} USD
- Méthode : {{ ucfirst($payment->payment_method) }}
- Réservation : {{ $payment->reservation->property->name ?? 'N/A' }}

@component('mail::button', ['url' => route('client.reservations.show', $payment->reservation)])
Voir la réservation
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent