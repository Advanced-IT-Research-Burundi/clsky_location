<!-- resources/views/client/payments/pay.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title mb-4">Paiement de la réservation</h2>

                    <div class="mb-4">
                        <h5>Récapitulatif</h5>
                        <p>{{ $reservation->property->title }}</p>
                        <p>Du {{ $reservation->check_in->format('d/m/Y') }} au {{ $reservation->check_out->format('d/m/Y') }}</p>
                        <p>Nombre de nuits : {{ $reservation->nights }}</p>
                        <p class="fw-bold">Montant total : {{ number_format($reservation->total_price, 2) }} USD</p>
                    </div>

                    <form id="payment-form">
                        <div id="payment-element" class="mb-3">
                            <!-- Stripe Elements will be inserted here -->
                        </div>
                        <div id="payment-message" class="alert alert-danger d-none"></div>
                        <button id="submit" class="btn btn-primary w-100">
                            <div class="spinner d-none" id="spinner"></div>
                            <span id="button-text">Payer maintenant</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config('services.stripe.key') }}');
let elements;
let paymentElement;

async function initialize() {
    const response = await fetch('{{ route("client.payments.process", $reservation) }}', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
    });

    const { clientSecret } = await response.json();

    elements = stripe.elements({ clientSecret });
    paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");
}

async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: '{{ route("client.reservations.show", $reservation) }}'
        }
    });

    if (error) {
        const messageContainer = document.getElementById('payment-message');
        messageContainer.textContent = error.message;
        messageContainer.classList.remove('d-none');
        setLoading(false);
    }
}

function setLoading(isLoading) {
    const button = document.getElementById('submit');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');

    if (isLoading) {
        button.disabled = true;
        spinner.classList.remove('d-none');
        buttonText.classList.add('d-none');
    } else {
        button.disabled = false;
        spinner.classList.add('d-none');
        buttonText.classList.remove('d-none');
    }
}

initialize();
document.getElementById('payment-form').addEventListener('submit', handleSubmit);
</script>
@endpush
@endsection
