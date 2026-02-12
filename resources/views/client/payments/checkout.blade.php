@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- alert message d'information succes or error --}}
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


    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Résumé de la réservation -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Détails de la réservation #{{ $reservation->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-1"><strong>{{ $reservation->property->titre }}</strong></p>
                            <p class="mb-1">Du {{ $reservation->check_in->format('d/m/Y') }}</p>
                            <p class="mb-1">Au {{ $reservation->check_out->format('d/m/Y') }}</p>
                            <p class="mb-0">{{ $reservation->guests }} voyageurs</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4 class="text-primary mb-0">{{ number_format($reservation->total_price, 2) }} USD</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options de paiement -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Instructions de paiement</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <p class="mb-0">Veuillez effectuer le paiement en utilisant l'une des méthodes suivantes avant de confirmer votre transaction :</p>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Virement bancaire -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="fw-bold mb-3">Virement bancaire</h6>
                                <p class="mb-2"><strong>IBAN :</strong> FR76 XXXX XXXX XXXX</p>
                                <p class="mb-2"><strong>BIC :</strong> XXXXXXXX</p>
                                <p class="mb-0"><strong>Bénéficiaire :</strong> Nom Société</p>
                            </div>
                        </div>

                        <!-- Mobile Money -->
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="fw-bold mb-3">Mobile Money</h6>
                                <p class="mb-2"><strong>Orange Money :</strong> +XX XXX XXX XXX</p>
                                <p class="mb-2"><strong>Wave :</strong> +XX XXX XXX XXX</p>
                                <p class="mb-0"><strong>Nom :</strong> Nom Société</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de confirmation -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Confirmer votre paiement</h5>
                </div>
                <div class="card-body">
                    <!-- Correction ici : Utilisation de POST et ajout du token CSRF -->
                    <form method="POST"  action="{{ route('client.payments.pay', $reservation) }}"  enctype="multipart/form-data">
                        @method('post')
                        @csrf

                        <div class="mb-3">
                            <label class="form-label required">Mode de paiement utilisé</label>
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

                        <div class="mb-3">
                            <label class="form-label required">Numéro de transaction</label>
                            <input type="text"
                                   name="transaction_id"
                                   class="form-control @error('transaction_id') is-invalid @enderror"
                                   placeholder="Ex: TRX123456789"
                                   required>
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Nom de l'expéditeur</label>
                            <input type="text"
                                   name="sender_name"
                                   class="form-control @error('sender_name') is-invalid @enderror"
                                   value="{{ old('sender_name', Auth::user()->name) }}"
                                   required>
                            @error('sender_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Téléphone de l'expéditeur</label>
                            <input type="tel"
                                   name="sender_phone"
                                   class="form-control @error('sender_phone') is-invalid @enderror"
                                   value="{{ old('sender_phone', Auth::user()->telephone) }}"
                                   required>
                            @error('sender_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Date du transfert</label>
                            <input type="datetime-local"
                                   name="transfer_date"
                                   class="form-control @error('transfer_date') is-invalid @enderror"
                                   value="{{ old('transfer_date', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('transfer_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Commentaire (optionnel)</label>
                            <textarea name="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                                <!-- Dans le formulaire, avant le bouton de soumission -->
                        <div class="mb-4">
                            <label class="form-label required">Justificatif de paiement</label>
                            <input type="file"
                                name="proof_document"
                                class="form-control @error('proof_document') is-invalid @enderror"
                                accept="image/*,.pdf"
                                required>
                            <div class="form-text">
                                Formats acceptés : PDF, JPG, PNG (Max: 5MB)
                            </div>
                            @error('proof_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            Confirmer le paiement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}
.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 1rem;
}
.form-label.required:after {
    content: " *";
    color: red;
}
.alert-info {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #333;
}
.btn-lg {
    padding: 0.75rem 1.25rem;
    font-size: 1.1rem;
}
</style>
@endsection
