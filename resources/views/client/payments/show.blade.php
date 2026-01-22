@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <a href="{{ route('client.payments.index') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Retour aux paiements
                </a>
            </div>

            <!-- Détails du paiement -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Détails du paiement #{{ $payment->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong class="text-muted">Date :</strong><br>
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="mb-1">
                                <strong class="text-muted">Méthode :</strong><br>
                                {{ ucfirst($payment->payment_method) }}
                            </p>
                            <p class="mb-1">
                                <strong class="text-muted">Transaction ID :</strong><br>
                                {{ $payment->transaction_id }}
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1">
                                <strong class="text-muted">Montant :</strong><br>
                                <span class="h3">{{ number_format($payment->amount, 2) }} USD</span>
                            </p>
                            <p class="mb-0">
                                <strong class="text-muted">Statut :</strong><br>
                                <span class="badge bg-{{ $payment->status_color }}">
                                    {{ $payment->status_text }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($payment->metadata)
                        <div class="border-top pt-4">
                            <h6 class="mb-3">Informations complémentaires</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong class="text-muted">Nom de l'expéditeur :</strong><br>
                                        {{ $payment->metadata['sender_name'] ?? 'N/A' }}
                                    </p>
                                    <p class="mb-1">
                                        <strong class="text-muted">Téléphone :</strong><br>
                                        {{ $payment->metadata['sender_phone'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong class="text-muted">Date du transfert :</strong><br>
                                        {{ isset($payment->metadata['transfer_date']) ?
                                            \Carbon\Carbon::parse($payment->metadata['transfer_date'])->format('d/m/Y H:i') :
                                            'N/A' }}
                                    </p>
                                    @if(isset($payment->metadata['notes']))
                                        <p class="mb-1">
                                            <strong class="text-muted">Notes :</strong><br>
                                            {{ $payment->metadata['notes'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Détails de la réservation associée -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Réservation associée</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>{{ $payment->reservation->property->titre }}</h6>
                            <p class="text-muted mb-1">
                                Du {{ $payment->reservation->check_in->format('d/m/Y') }}
                                au {{ $payment->reservation->check_out->format('d/m/Y') }}
                            </p>
                            <p class="mb-0">
                                {{ $payment->reservation->guests }} voyageurs
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('client.reservations.show', $payment->reservation) }}"
                               class="btn btn-outline-primary">
                                Voir la réservation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
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
        </div>
    </div>
</div>
@endsection

<style>

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
