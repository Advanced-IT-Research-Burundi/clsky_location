@extends('layouts.admin')

@section('title', 'Nouvelle Réservation')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouvelle Réservation</h1>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
                        @csrf

                        <!-- Sélection de la propriété -->
                        <div class="mb-4">
                            <label for="property_id" class="form-label">Propriété</label>
                            <select class="form-select @error('property_id') is-invalid @enderror" 
                                    name="property_id" 
                                    id="property_id" 
                                    required>
                                <option value="">Sélectionnez une propriété</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" 
                                            data-price="{{ $property->price }}"
                                            {{ old('property_id', $selectedProperty ?? '') == $property->id ? 'selected' : '' }}>
                                        {{ $property->title }} - {{ number_format($property->price) }}€/nuit
                                    </option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dates de réservation -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="check_in" class="form-label">Date d'arrivée</label>
                                <input type="date" 
                                       class="form-control @error('check_in') is-invalid @enderror" 
                                       id="check_in" 
                                       name="check_in" 
                                       value="{{ old('check_in') }}"
                                       min="{{ date('Y-m-d') }}"
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
                                       value="{{ old('check_out') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
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
                                   value="{{ old('guests', 1) }}"
                                   min="1"
                                   required>
                            @error('guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Créer la réservation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Résumé et suggestions -->
        <div class="col-md-4">
            <!-- Résumé de la réservation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Résumé</h5>
                </div>
                <div class="card-body">
                    <div id="reservationSummary">
                        <div class="mb-3">
                            <label class="fw-bold">Durée du séjour</label>
                            <p id="duration">-- nuits</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Prix par nuit</label>
                            <p id="pricePerNight">-- BIF</p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="fw-bold">Total</label>
                            <p id="totalPrice" class="h4 text-primary">-- BIF</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions de disponibilités -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Périodes disponibles suggérées</h5>
                </div>
                <div class="card-body">
                    <div id="availabilitySuggestions">
                        <!-- Les suggestions seront chargées dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.suggestion-item {
    cursor: pointer;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.suggestion-item:hover {
    background-color: var(--bs-primary-bg-subtle);
}

.price-badge {
    font-size: 0.9em;
    padding: 4px 8px;
    border-radius: 20px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservationForm');
    const propertySelect = document.getElementById('property_id');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const guestsInput = document.getElementById('guests');

    // Mise à jour du résumé
    function updateSummary() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const propertyOption = propertySelect.selectedOptions[0];

        if (checkIn && checkOut && propertyOption) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const pricePerNight = parseFloat(propertyOption.dataset.price);
            const total = nights * pricePerNight;

            document.getElementById('duration').textContent = `${nights} nuit${nights > 1 ? 's' : ''}`;
            document.getElementById('pricePerNight').textContent = `${pricePerNight.toFixed(2)} BIF`;
            document.getElementById('totalPrice').textContent = `${total.toFixed(2)} BIF`;
        }
    }

    // Charger les suggestions de disponibilités
    function loadSuggestions() {
        const propertyId = propertySelect.value;
        if (!propertyId) return;

        // Utiliser la nouvelle route nommée
        fetch(`{{ url('/properties') }}/${propertyId}/availability-suggestions`)
            .then(response => response.json())
            .then(data => {
                const suggestionsDiv = document.getElementById('availabilitySuggestions');
                suggestionsDiv.innerHTML = '';

                if (data.length === 0) {
                    suggestionsDiv.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-x fs-2"></i>
                            <p class="mt-2">Aucune suggestion disponible</p>
                        </div>
                    `;
                    return;
                }

                data.forEach(suggestion => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item mb-2';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">${suggestion.checkIn} - ${suggestion.checkOut}</div>
                                <small class="text-muted">${suggestion.nights} nuits</small>
                            </div>
                            <span class="price-badge bg-primary-subtle text-primary">
                                ${suggestion.totalPrice} BIF
                            </span>
                        </div>
                    `;
                    
                    div.addEventListener('click', () => {
                        checkInInput.value = suggestion.checkIn;
                        checkOutInput.value = suggestion.checkOut;
                        updateSummary();
                    });

                    suggestionsDiv.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('availabilitySuggestions').innerHTML = `
                    <div class="alert alert-danger mb-0">
                        Une erreur est survenue lors du chargement des suggestions.
                    </div>
                `;
            });
    }

    // Events listeners
    [propertySelect, checkInInput, checkOutInput, guestsInput].forEach(input => {
        input.addEventListener('change', updateSummary);
    });

    propertySelect.addEventListener('change', loadSuggestions);

    // Validation des dates
    checkInInput.addEventListener('change', function() {
        const minCheckOut = new Date(this.value);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        checkOutInput.min = minCheckOut.toISOString().split('T')[0];
        
        if (checkOutInput.value && new Date(checkOutInput.value) <= new Date(this.value)) {
            checkOutInput.value = minCheckOut.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
@endsection
