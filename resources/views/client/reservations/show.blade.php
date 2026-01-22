@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- alert message d'information succes or error --}}
    @if(session('success'))
    <div class="alert alert-success" role="alert">
            {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger" role="alert">
            {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
    @endif
    <!-- Formulaire de modification de la réservation -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Détails de la réservation -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la réservation</h5>
                    <span class="badge bg-light text-primary">{{ ucfirst($reservation->statut) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Dates de séjour</h6>
                            <p class="mb-0">
                                <i class="bi bi-calendar"></i>
                                Du {{ $reservation->check_in->format('d/m/Y') }}
                                au {{ $reservation->check_out->format('d/m/Y') }}
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-clock"></i>
                                {{ $reservation->check_in->diffInDays($reservation->check_out) }} nuits
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Voyageurs</h6>
                            <p class="mb-0">
                                <i class="bi bi-people"></i>
                                {{ $reservation->guests }} personnes
                            </p>
                        </div>
                    </div>

                    @if($reservation->notes)
                    <div class="mb-4">
                        <h6 class="text-muted">Notes</h6>
                        <p class="mb-0">{{ $reservation->notes }}</p>
                    </div>
                    @endif

                    <div class="border-top pt-3">
                        <h6 class="text-muted">Prix total</h6>
                        <h4 class="mb-0">{{ number_format($reservation->total_price, 2) }} USD</h4>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    @if($reservation->status !== 'annulee')
                        @if($reservation->check_in->isFuture() && $reservation->check_in->diffInDays(now()) > 2)
                            <form action="{{ route('client.reservations.cancel', $reservation) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                    Annuler la réservation
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Détails de la propriété -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Propriété réservée</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($reservation->property->images->isNotEmpty())
                                <img src="{{ Storage::url($reservation->property->images->first()->image_path) }}"
                                    alt="{{ $reservation->property->titre }}"
                                    class="img-fluid rounded">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $reservation->property->titre }}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt"></i>
                                {{ $reservation->property->adresse }}, {{ $reservation->property->ville }}
                            </p>
                            <div class="d-flex mb-3">
                                <span class="me-3">
                                    <i class="bi bi-door-closed"></i>
                                    {{ $reservation->property->chambres }} chambres
                                </span>
                                <span class="me-3">
                                    <i class="bi bi-droplet"></i>
                                    {{ $reservation->property->salles_bain }} sdb
                                </span>
                                <span>
                                    <i class="bi bi-rulers"></i>
                                    {{ $reservation->property->surface }} m²
                                </span>
                            </div>
                            <a href="{{ route('client.properties.show', $reservation->property) }}"
                               class="btn btn-outline-primary">
                                Voir la propriété
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- État du paiement -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">État du paiement</h5>
                </div>
                <div class="card-body">
                    @if($reservation->payments->isEmpty())
                        <div class="text-center">
                            <p class="mb-3">Aucun paiement effectué</p>
                            <a href="{{ route('client.payments.initiate', $reservation) }}" class="btn btn-primary">
                                Procéder au paiement
                            </a>

                        </div>
                    @else
                        @foreach($reservation->payments as $payment)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0">Paiement #{{ $payment->id }}</h6>
                                    <small class="text-muted">
                                        {{ $payment->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">{{ number_format($payment->amount, 2) }} USD</h6>
                                    <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Support -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Besoin d'aide ?</h5>
                </div>
                <div class="card-body">
                    <p>Notre équipe est disponible 24/7 pour vous aider.</p>
                    <a href="{{route('messages.create')}}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-chat-dots"></i>
                        Contacter le support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        background-color: #fff;
        padding: 1rem;
    }
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid rgba(0,0,0,.125);
        padding: 1rem;
    }
    .badge {
        padding: 0.5em 1em;
        border-radius: 30px;
    }
    .bi {
        margin-right: 0.5rem;
    }
</style>
@endsection
