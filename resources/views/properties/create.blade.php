@extends('layouts.admin')

@section('title', 'Nouvelle Propriété')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Ajouter une propriété</h1>
        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data" id="property-form">
        @csrf
        
        {{-- Inclure le formulaire partagé --}}
        @include('properties._form')

        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('properties.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="bi bi-save"></i> Créer la propriété
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css" rel="stylesheet">
<style>
    .dropzone {
        border: 2px dashed #0d6efd;
        border-radius: 8px;
        background: #f8f9fa;
        min-height: 200px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    .dropzone:hover {
        background: #e9ecef;
        border-color: #0b5ed7;
    }
    .dropzone .dz-message {
        text-align: center;
        margin: 2em 0;
    }
    .dropzone .dz-preview {
        margin: 10px;
    }
    .dropzone .dz-preview .dz-image {
        border-radius: 8px;
        overflow: hidden;
        width: 120px;
        height: 120px;
    }
    .dropzone .dz-preview .dz-image img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;
    
    // Tableau pour stocker les fichiers
    let uploadedFiles = [];
    
    // Configuration Dropzone
    const dropzone = new Dropzone("#imageDropzone", {
        url: "#", // URL factice, on soumettra avec le formulaire
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 20,
        maxFiles: 20,
        maxFilesize: 5, // MB
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        dictDefaultMessage: "Glissez-déposez vos images ici ou cliquez pour sélectionner",
        dictRemoveFile: "Supprimer",
        dictCancelUpload: "Annuler",
        dictMaxFilesExceeded: "Vous ne pouvez pas ajouter plus de 20 images",
        thumbnailWidth: 120,
        thumbnailHeight: 120,
        
        init: function() {
            const dz = this;
            
            // Quand on ajoute un fichier
            this.on("addedfile", function(file) {
                console.log("Fichier ajouté:", file.name);
                uploadedFiles.push(file);
            });
            
            // Quand on supprime un fichier
            this.on("removedfile", function(file) {
                console.log("Fichier supprimé:", file.name);
                uploadedFiles = uploadedFiles.filter(f => f !== file);
            });
            
            // Gestion de la soumission du formulaire
            document.getElementById('property-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const formData = new FormData(form);
                
                // Ajouter les fichiers de Dropzone au FormData
                uploadedFiles.forEach(function(file) {
                    formData.append('images[]', file);
                });
                
                // Désactiver le bouton de soumission
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...';
                
                // Envoyer le formulaire avec fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json().catch(() => {
                            // Si la réponse n'est pas JSON, c'est probablement une redirection
                            window.location.href = "{{ route('properties.index') }}";
                        });
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Erreur lors de la création');
                        });
                    }
                })
                .then(data => {
                    if (data && data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = "{{ route('properties.index') }}";
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la création de la propriété: ' + error.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save"></i> Créer la propriété';
                });
            });
        }
    });
</script>
@endpush