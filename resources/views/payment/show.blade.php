@extends('layouts.admin')

@section('title', 'Détails du paiement')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Détails du paiement #{{ $payment->id }}</h1>
        <div>
            <div class="btn-group">
                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <a href="#" class="btn btn-success" onclick="printReceipt()">
                    <i class="bi bi-printer"></i> Imprimer
                </a>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du paiement</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Montant</h6>
                            <h3 class="mb-0">{{ number_format($payment->amount, 2) }} USD</h3>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Statut</h6>
                            <span class="badge bg-{{ $payment->status_color }} fs-6">
                                {{ $payment->status_text }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Méthode de paiement</h6>
                            <p class="mb-0">
                                @switch($payment->payment_method)
                                    @case('card')
                                        <i class="bi bi-credit-card me-2"></i> Carte bancaire
                                        @break
                                    @case('bank_transfer')
                                        <i class="bi bi-bank me-2"></i> Virement bancaire
                                        @break
                                    @case('cash')
                                        <i class="bi bi-cash me-2"></i> Espèces
                                        @break
                                @endswitch
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Date du paiement</h6>
                            <p class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        @if($payment->transaction_id)
                            <div class="col-12">
                                <h6 class="text-muted">Référence de transaction</h6>
                                <p class="mb-0">
                                    <i class="bi bi-upc me-2"></i>
                                    {{ $payment->transaction_id }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Réservation associée -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Détails de la réservation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Propriété</h6>
                            <p class="mb-3">
                                <a href="{{ route('properties.show', $payment->reservation->property) }}"
                                   class="text-decoration-none">
                                    {{ $payment->reservation->property->title }}
                                </a>
                            </p>

                            <h6 class="text-muted">Période</h6>
                            <p class="mb-0">
                                Du {{ $payment->reservation->check_in->format('d/m/Y') }}
                                au {{ $payment->reservation->check_out->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Client</h6>
                            <p class="mb-3">
                                <i class="bi bi-person me-2"></i>
                                {{ $payment->reservation->user->name }}
                            </p>

                            <h6 class="text-muted">Statut de la réservation</h6>
                            <span class="badge bg-{{ $payment->reservation->status_color }}">
                                {{ $payment->reservation->status_text }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions et informations additionnelles -->
        <div class="col-md-4">
            <!-- Actions rapides -->
            <!-- Aperçu du justificatif -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Justificatif de paiement</h5>
                @if($payment->proof_document_path)
                    <a href="{{ route('client.payments.download-proof', $payment) }}"
                    class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i> Télécharger
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($payment->proof_document_path)
                    @php
                        $extension = pathinfo(storage_path($payment->proof_document_path), PATHINFO_EXTENSION);
                        $isPdf = strtolower($extension) === 'pdf';
                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                    @endphp

                    <div class="document-preview">
                        @if($isPdf)
                            <!-- Aperçu PDF -->
                            <div class="ratio ratio-16x9 mb-3">
                                <iframe src="{{ Storage::url($payment->proof_document_path) }}"
                                        class="rounded"
                                        style="border: 1px solid #dee2e6;">
                                </iframe>
                            </div>
                        @elseif($isImage)
                            <!-- Aperçu Image -->
                            <div class="text-center">
                                <a href="{{ Storage::url($payment->proof_document_path)}}"
                                target="_blank"
                                class="d-inline-block position-relative">
                                    <img src="{{ Storage::url($payment->proof_document_path) }}"
                                        alt="Justificatif de paiement"
                                        class="img-fluid rounded"
                                        style="max-height: 400px;">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-dark">
                                            <i class="bi bi-arrows-fullscreen"></i> Agrandir
                                        </span>
                                    </div>
                                </a>
                            </div>
                        @else
                            <!-- Format non pris en charge -->
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-text display-4 text-muted"></i>
                                <p class="mt-2 mb-0">
                                    Le format du fichier ne permet pas l'aperçu.<br>
                                    Veuillez télécharger le document pour le consulter.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Informations sur le fichier -->
                    <div class="mt-3 small text-muted">
                        <p class="mb-0">
                            <strong>Nom du fichier :</strong>
                            {{ $payment->metadata['original_filename'] ?? basename($payment->proof_document_path) }}
                        </p>
                        <p class="mb-0">
                            <strong>Type :</strong>
                            {{ strtoupper($extension) }}
                        </p>
                    </div>
                @else
                    <!-- Aucun document -->
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-x display-4 text-muted"></i>
                        <p class="mt-2 mb-0">Aucun justificatif n'a été fourni</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($payment->status === 'pending')
                        <form action="{{ route('payments.update', $payment) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Marquer comme payé
                            </button>
                        </form>
                    @endif

                    @if($payment->status === 'completed')
                        <form action="{{ route('payments.update', $payment) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="refunded">
                            <button type="submit" class="btn btn-warning w-100"
                                    onclick="return confirm('Êtes-vous sûr de vouloir rembourser ce paiement ?')">
                                <i class="bi bi-arrow-repeat"></i> Rembourser
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('payments.destroy', $payment) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <!-- Historique -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Historique</h5>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <small class="text-muted">Création</small>
                        <p class="mb-0">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($payment->updated_at != $payment->created_at)
                        <div class="list-group-item">
                            <small class="text-muted">Dernière modification</small>
                            <p class="mb-0">{{ $payment->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printReceipt() {
    window.print();
}
</script>
@endpush

@push('styles')
<style media="print">
    /* Styles pour l'impression */
    .btn-group, .card-header {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .document-preview {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .document-preview img {
        transition: transform 0.3s ease;
    }

    .document-preview a:hover img {
        transform: scale(1.02);
    }

    /* Style pour l'iframe PDF */
    .document-preview iframe {
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    /* Style pour le badge d'agrandissement */
    .document-preview .badge {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .document-preview a:hover .badge {
        opacity: 1;
    }

    /* Style pour les messages d'erreur ou absence de document */
    .document-preview .bi-file-earmark-x,
    .document-preview .bi-file-earmark-text {
        opacity: 0.5;
    }
    </style>

    <!-- Ajoutez ce script pour le viewer d'images -->

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser Lightbox pour les images
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'showImageNumberLabel': false,
            'disableScrolling': true
        });
    });
    </script>
@endpush
