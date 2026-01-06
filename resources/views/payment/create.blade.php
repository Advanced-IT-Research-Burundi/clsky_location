@extends('layouts.admin')

@section('title', 'Nouveau Paiement')

@section('content')
<div class="container-fluid">
    {{-- alert message error and success en utlisant session --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouveau Paiement</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf

                        <!-- Réservation -->
                        <div class="mb-3">
                            <label for="reservation_id" class="form-label">Réservation</label>
                            <select class="form-select @error('reservaion_id') is-invalid @enderror"
                                    id="reservation_id"
                                    name="reservation_id"
                                    required>
                                <option value="">Sélectionner une réservation</option>
                                @foreach($reservations ?? [] as $reservation)
                                    <option value="{{ $reservation->id }}"
                                            data-amount="{{ $reservation->total_price }}"
                                            {{ old('reservation_id') == $reservation->id ? 'selected' : '' }}>
                                        #{{ $reservation->id }} - {{ $reservation->property->title }}
                                        ({{ number_format($reservation->total_price, 2) }} BIF)
                                    </option>t
                                @endforeach
                            </select>
                            @error('reservation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Montant -->
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant</label>
                            <div class="input-group">
                                <input type="number"
                                       step="0.01"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       id="amount"
                                       name="amount"
                                       value="{{ old('amount') }}"
                                       required>
                                <span class="input-group-text">BIF</span>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Méthode de paiement -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Méthode de paiement</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror"
                                    id="payment_method"
                                    name="payment_method"
                                    required>
                                <option value="">Sélectionner une méthode</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>
                                    Carte bancaire
                                </option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                                    Virement bancaire
                                </option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>
                                    Espèces
                                </option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Référence de transaction -->
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Référence de transaction</label>
                            <input type="text"
                                   class="form-control @error('transaction_id') is-invalid @enderror"
                                   id="transaction_id"
                                   name="transaction_id"
                                   value="{{ old('transaction_id') }}">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Optionnel - Référence externe du paiement
                            </small>
                        </div>

                        <!-- Statut -->
                        <div class="mb-4">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                    En attente
                                </option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                    Complété
                                </option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>
                                    Échoué
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Créer le paiement
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aide et informations -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Méthodes de paiement</h6>
                        <hr>
                        <ul class="mb-0">
                            <li><strong>Carte bancaire:</strong> Paiement immédiat</li>
                            <li><strong>Virement:</strong> Délai de traitement 2-3 jours</li>
                            <li><strong>Espèces:</strong> Paiement sur place uniquement</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check"></i> Statuts
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <span class="badge bg-warning">En attente</span>
                            <small class="ms-2">Paiement initié mais non confirmé</small>
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-success">Complété</span>
                            <small class="ms-2">Paiement reçu et validé</small>
                        </li>
                        <li>
                            <span class="badge bg-danger">Échoué</span>
                            <small class="ms-2">Problème lors du paiement</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reservationSelect = document.getElementById('reservation_id');
    const amountInput = document.getElementById('amount');

    // Mise à jour automatique du montant
    reservationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const amount = selectedOption.dataset.amount;
        if (amount) {
            amountInput.value = amount;
        }
    });

    // Trigger initial si une réservation est présélectionnée
    if (reservationSelect.value) {
        reservationSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
