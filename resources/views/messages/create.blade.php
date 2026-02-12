@extends('layouts.admin')

@section('title', 'Nouveau message')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouveau message</h1>
        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Destinataire -->
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">Destinataire</label>
                            <select class="form-select @error('receiver_id') is-invalid @enderror" id="receiver_id"
                                    name="receiver_id" required>
                                <option value="">Sélectionnez un destinataire</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role_text }})
                                    </option>
                                @endforeach
                            </select>
                            @error('receiver_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Propriété (optionnel) -->
                        @if(isset($properties) && $properties->count() > 0)
                            <div class="mb-3">
                                <label for="property_id" class="form-label">Concernant la propriété (optionnel)</label>
                                <select class="form-select @error('property_id') is-invalid @enderror" id="property_id"
                                        name="property_id">
                                    <option value="">Sélectionnez une propriété</option>
                                    @foreach ($properties as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                            {{ $property->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Sujet -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                   id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="content" class="form-label">Message</label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pièces jointes -->
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Pièces jointes</label>
                            <input type="file" class="form-control" name="attachments[]" multiple
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="text-muted">
                                Formats acceptés : jpg, png, pdf, doc, docx (max 10MB par fichier)
                            </small>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('messages.index') }}" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Conseils et templates rapides -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb text-warning"></i> Conseils
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Soyez clair et concis</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Incluez toutes les informations pertinentes</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Si le message concerne une propriété, sélectionnez-la</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Relisez votre message avant l'envoi</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning text-primary"></i> Templates rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary text-start" onclick="useTemplate('demande-info')">Demande d'informations</button>
                        <button type="button" class="btn btn-outline-secondary text-start" onclick="useTemplate('reservation')">Question sur une réservation</button>
                        <button type="button" class="btn btn-outline-secondary text-start" onclick="useTemplate('disponibilite')">Vérification de disponibilité</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
function useTemplate(type) {
    const templates = {
        'demande-info': {
            subject: 'Demande d\'informations complémentaires',
            content: 'Bonjour,\n\nJe souhaiterais obtenir plus d\'informations concernant...\n\nCordialement,'
        },
        'reservation': {
            subject: 'Question concernant ma réservation',
            content: 'Bonjour,\n\nJ\'aurais une question concernant la réservation...\n\nCordialement,'
        },
        'disponibilite': {
            subject: 'Vérification de disponibilité',
            content: 'Bonjour,\n\nJe souhaiterais savoir si la propriété est disponible pour la période du... au...\n\nCordialement,'
        }
    };

    const template = templates[type];
    if(template){
        document.getElementById('subject').value = template.subject;
        document.getElementById('content').value = template.content;
    }
}

// Initialisation Choices.js
document.addEventListener('DOMContentLoaded', function() {
    const select = document.querySelector('#receiver_id');
    if(select){
        new Choices(select, {
            searchEnabled: true,
            searchPlaceholderValue: 'Rechercher un utilisateur...',
            noResultsText: 'Aucun utilisateur trouvé'
        });
    }
});
</script>
@endpush
@endsection
