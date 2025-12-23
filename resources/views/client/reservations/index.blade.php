@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-4">Mes réservations</h2>
        </div>
    </div>

    <div class="row">
        <!-- Filtres -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filtres</h5>
                    <form action="{{ route('client.reservations.index') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="">Tous</option>
                                <option value="pending" {{ request('status') === 'en_attente' ? 'selected' : '' }}>
                                    En attente
                                </option>
                                <option value="confirmed" {{ request('status') === 'confirmee' ? 'selected' : '' }}>
                                    Confirmée
                                </option>
                                <option value="cancelled" {{ request('status') === 'annulee' ? 'selected' : '' }}>
                                    Annulée
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Appliquer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des réservations -->
        <div class="col-lg-9">
            @forelse($reservations as $reservation)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <!-- Image de la propriété -->
                            <div class="col-md-3">
                                @if($reservation->property->images->isNotEmpty())
                                    <img src="{{ Storage::url($reservation->property->images->first()->image_path) }}"
                                         alt="{{ $reservation->property->titre }}"
                                         class="img-fluid rounded">
                                @endif
                            </div>

                            <!-- Détails de la réservation -->
                            <div class="col-md-6">
                                <h5 class="mb-2">{{ $reservation->property->titre }}</h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar"></i>
                                    Du {{ $reservation->check_in->format('d/m/Y') }}
                                    au {{ $reservation->check_out->format('d/m/Y') }}
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-people"></i>
                                    {{ $reservation->guests }} voyageurs
                                </p>
                                <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' :
                                                       ($reservation->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="col-md-3 text-end">
                                <div class="mb-2">
                                    <h6 class="mb-0">Total</h6>
                                    <h5 class="text-primary mb-3">{{ number_format($reservation->total_price, 2) }} BIF</h5>
                                </div>
                                <a href="{{ route('client.reservations.show', $reservation) }}"
                                   class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    Voir les détails
                                </a>
                                @if($reservation->status === 'pending')
                                    <a href="{{ route('client.payments.initiate', $reservation) }}"
                                       class="btn btn-primary btn-sm w-100">
                                        Payer maintenant
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                        <h4>Aucune réservation</h4>
                        <p class="text-muted">Vous n'avez pas encore de réservation.</p>
                        <a href="{{ route('client.properties.index') }}" class="btn btn-primary">
                            Explorer les propriétés
                        </a>
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
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
