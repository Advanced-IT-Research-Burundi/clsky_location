@extends('layouts.admin')

@section('title', 'Modifier la réservation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier la réservation #{{ $reservation->id }}</h1>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <!-- Formulaire d'édition -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('reservations.update', $reservation) }}" method="POST" id="reservationForm">
                        @csrf
                        @method('PUT')

                        <!-- Propriété (en lecture seule) -->
                        <div class="mb-4">
                            <label class="form-label">Propriété</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $reservation->property->title }}" 
                                   readonly>
                            <input type="hidden" 
                                   name="property_id" 
                                   value="{{ $reservation->property_id }}">
                        </div>

                        <!-- Dates de réservation -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">Date d'arrivée</label>
                                <input type="date" 
                                       class="form-control @error('check_in') is-invalid @enderror" 
                                       id="check_in" 
                                       name="check_in" 
                                       value="{{ old('check_in', $reservation->check_in->format('Y-m-d')) }}"
                                       required>
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="check_out" class="form-label">Date de départ</label>
                                <input type="date" 
                                       class="form-control @error('check_out') is-invalid @enderror" 
                                       id="check_out" 
                                       name="check_out" 
                                       value="{{ old('check_out', $reservation->check_out->format('Y-m-d')) }}"
                                       required>
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nombre d'invités -->
                        <div class="mb-4">
                            <label for="guests" class="form-label">Nombre d'invités</label>
                            <input type="number" 
                                   class="form-control @error('guests') is-invalid @enderror" 
                                   id="guests" 
                                   name="guests" 
                                   value="{{ old('guests', $reservation->guests) }}"
                                   min="1"
                                   required>
                            @error('guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="pending" {{ old('status', $reservation->status) === 'pending' ? 'selected' : '' }}>
                                    En attente
                                </option>
                                <option value="confirmed" {{ old('status', $reservation->status) === 'confirmed' ? 'selected' : '' }}>
                                    Confirmée
                                </option>
                                <option value="cancelled" {{ old('status', $reservation->status) === 'cancelled' ? 'selected' : '' }}>
                                    Annulée
                                </option>
                                <option value="completed" {{ old('status', $reservation->status) === 'completed' ? 'selected' : '' }}>
                                    Terminée
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes', $reservation->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Résumé et propriété -->
        <div class="col-md-4">
            <!-- Résumé -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Résumé</h5>
                </div>
                <div class="card-body">
                    <div id="reservationSummary">
                        <div class="mb-3">
                            <label class="fw-bold">Durée du séjour</label>
                            <p id="duration">
                                {{ $reservation->check_in->diffInDays($reservation->check_out) }} nuits
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Prix par nuit</label>
                            <p id="pricePerNight">{{ $reservation->property->price }} BIF</p>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="fw-bold">Total</label>
                            <p id="totalPrice" class="h4 text-primary mb-0">
                                {{ $reservation->total_price }} BIF
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails de la propriété -->
            <div class="card">
                <img src="{{ $reservation->property->images->where('is_primary', true)->first() 
                    ? Storage::url($reservation->property->images->where('is_primary', true)->first()->image_path)
                    : 'placeholder.jpg' }}" 
                     class="card-img-top" 
                     alt="{{ $reservation->property->title }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $reservation->property->title }}</h5>
                    <p class="text-muted mb-3">
                        <i class="bi bi-geo-alt me-2"></i>
                        {{ $reservation->property->address }}
                    </p>
                    <div class="d-flex justify-content-between">
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
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    function updateSummary() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (checkIn && checkOut) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const pricePerNight = {{ $reservation->property->price }};
            const total = nights * pricePerNight;

            document.getElementById('duration').textContent = `${nights} nuit${nights > 1 ? 's' : ''}`;
            document.getElementById('totalPrice').textContent = `${total.toFixed(2)} BIF`;
        }
    }

    checkInInput.addEventListener('change', function() {
        const minCheckOut = new Date(this.value);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        checkOutInput.min = minCheckOut.toISOString().split('T')[0];
        
        if (checkOutInput.value && new Date(checkOutInput.value) <= new Date(this.value)) {
            checkOutInput.value = minCheckOut.toISOString().split('T')[0];
        }
        
        updateSummary();
    });

    checkOutInput.addEventListener('change', updateSummary);
});
</script>
@endpush
@endsection