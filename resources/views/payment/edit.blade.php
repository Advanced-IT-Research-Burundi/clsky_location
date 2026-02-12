@extends('layouts.admin')

@section('title', 'Modifier le paiement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier le paiement #{{ $payment->id }}</h1>
        <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('payments.update', $payment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Réservation (en lecture seule) -->
                        <div class="mb-3">
                            <label class="form-label">Réservation</label>
                            <input type="text"
                                   class="form-control"
                                   value="Réservation #{{ $payment->reservation->id }} - {{ $payment->reservation->property->title }}"
                                   readonly>
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
                                       value="{{ old('amount', $payment->amount) }}"
                                       required>
                                <span class="input-group-text">USD</span>
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
                                <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>
                                    Carte bancaire
                                </option>
                                <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>
                                    Virement bancaire
                                </option>
                                <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>
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
                                   value="{{ old('transaction_id', $payment->transaction_id) }}">
                            @error('transaction_id')
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
                                <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>
                                    En attente
                                </option>
                                <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>
                                    Complété
                                </option>
                                <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>
                                    Échoué
                                </option>
                                <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>
                                    Remboursé
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('payments.show', $payment) }}" class="btn btn-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informations de la réservation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Détails de la réservation</h5>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Client</dt>
                        <dd>{{ $payment->reservation->user->name }}</dd>

                        <dt>Propriété</dt>
                        <dd>{{ $payment->reservation->property->title }}</dd>

                        <dt>Période</dt>
                        <dd>
                            Du {{ $payment->reservation->check_in->format('d/m/Y') }}
                            au {{ $payment->reservation->check_out->format('d/m/Y') }}
                        </dd>

                        <dt>Montant total</dt>
                        <dd>{{ number_format($payment->reservation->total_price, 2) }} USD</dd>
                    </dl>
                </div>
            </div>

            <!-- Note d'information -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle"></i> Important
                    </h6>
                    <p class="card-text small">
                        La modification du statut du paiement peut affecter le statut de la réservation associée.
                        Assurez-vous de bien vérifier les informations avant d'enregistrer les modifications.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation avant changement de statut important
    const statusSelect = document.getElementById('status');
    const originalStatus = '{{ $payment->status }}';

    document.querySelector('form').addEventListener('submit', function(e) {
        if (statusSelect.value !== originalStatus) {
            if (!confirm('Êtes-vous sûr de vouloir modifier le statut du paiement ?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush
@endsection
