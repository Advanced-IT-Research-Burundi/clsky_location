
{{-- resources/views/reservations/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Détails de la réservation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Détails de la réservation #{{ $reservation->id }}</h1>
        <div>
            <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Informations de la réservation</h5>
                    <span class="badge bg-{{ $reservation->status_color }}">
                        {{ $reservation->status_text }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Dates du séjour</h6>
                            <p class="mb-1">
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                Arrivée : {{ $reservation->check_in->format('d/m/Y') }}
                            </p>
                            <p>
                                <i class="bi bi-calendar-x text-primary me-2"></i>
                                Départ : {{ $reservation->check_out->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Détails</h6>
                            <p class="mb-1">
                                <i class="bi bi-people text-primary me-2"></i>
                                {{ $reservation->guests }} invité(s)
                            </p>
                            <p>
                                <i class="bi bi-clock text-primary me-2"></i>
                                {{ $reservation->check_in->diffInDays($reservation->check_out) }} nuit(s)
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Client</h6>
                            <p class="mb-1">
                                <i class="bi bi-person text-primary me-2"></i>
                                {{ $reservation->user->name }}
                            </p>
                            <p class="mb-1">
                                <i class="bi bi-envelope text-primary me-2"></i>
                                {{ $reservation->user->email }}
                            </p>
                            @if($reservation->user->phone)
                                <p>
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    {{ $reservation->user->phone }}
                                </p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Paiement</h6>
                            <p class="mb-1">
                                <i class="bi bi-cash text-primary me-2"></i>
                                Total : {{ number_format($reservation->total_price, 2) }} BIF
                            </p>
                            <p>
                                <i class="bi bi-credit-card text-primary me-2"></i>
                                Statut : {{ $reservation->payment_status_text }}
                            </p>
                        </div>

                        @if($reservation->notes)
                            <div class="col-12">
                                <h6 class="text-muted">Notes</h6>
                                <p class="mb-0">{{ $reservation->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Historique</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Réservation créée</h6>
                                <small class="text-muted">
                                    {{ $reservation->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                        @if($reservation->status === 'confirmed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Réservation confirmée</h6>
                                    <small class="text-muted">
                                        {{ $reservation->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Propriété -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Propriété réservée</h5>
                </div>
                @if($reservation->property->images->where('is_primary', true)->first())
                    <img src="{{ Storage::url($reservation->property->images->where('is_primary', true)->first()->image_path) }}" 
                         class="card-img-top" 
                         alt="{{ $reservation->property->title }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $reservation->property->title }}</h5>
                    <p class="text-muted mb-3">
                        <i class="bi bi-geo-alt me-2"></i>
                        {{ $reservation->property->address }}
                    </p>

                    <div class="d-flex justify-content-between mb-3">
                        <span>
                            <i class="bi bi-door-open me-1"></i>
                            {{ $reservation->property->bedrooms }} ch.
                        </span>
                        <span>
                            <i class="bi bi-droplet me-1"></i>
                            {{ $reservation->property->bathrooms }} sdb.
                        </span>
                        <span>
                            <i class="bi bi-arrows-angle-expand me-1"></i>
                            {{ $reservation->property->area }} m²
                        </span>
                    </div>

                    <a href="{{ route('properties.show', $reservation->property) }}" 
                       class="btn btn-outline-primary w-100">
                        <i class="bi bi-building"></i> Voir la propriété
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reservations.destroy', $reservation) }}" 
                          method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                        @csrf
                        @method('DELETE')

                        @if($reservation->status === 'pending')
                            <button type="button" 
                                    class="btn btn-success w-100 mb-2" 
                                    onclick="updateStatus('confirmed')">
                                <i class="bi bi-check-circle"></i> Confirmer la réservation
                            </button>
                        @endif

                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Annuler la réservation
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    height: 100%;
    width: 2px;
    background: var(--bs-gray-200);
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: 0.35rem;
    width: 1.3rem;
    height: 1.3rem;
    border-radius: 50%;
}
</style>
@endpush

@push('scripts')
<script>
function updateStatus(status) {
    if (confirm('Êtes-vous sûr de vouloir modifier le statut de cette réservation ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('reservations.update', $reservation) }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
