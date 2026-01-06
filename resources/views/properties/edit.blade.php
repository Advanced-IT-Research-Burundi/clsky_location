@extends('layouts.admin')

@section('title', 'Modifier la propriété')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier la propriété</h1>
        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" id="property-form">
        @csrf
        {{-- Important: Utiliser un champ caché pour simuler PUT au lieu de @method('PUT') --}}
        <input type="hidden" name="_method" value="PUT">

        @include('properties._form', ['property' => $property])

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('properties.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annuler
                </a>
                <button type="submit" class="btn btn-success" id="submit-btn">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css" rel="stylesheet">
<style>
    .dropzone {
        border: 2px dashed #198754;
        border-radius: 8px;
        background: #f8f9fa;
        min-height: 200px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    .dropzone:hover {
        background: #e9ecef;
        border-color: #157347;
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
    
    let uploadedFiles = [];
    
    const dropzone = new Dropzone("#imageDropzone", {
        url: "#",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 20,
        maxFiles: 20,
        maxFilesize: 5,
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
            
            this.on("addedfile", function(file) {
                console.log("Fichier ajouté:", file.name);
                uploadedFiles.push(file);
            });
            
            this.on("removedfile", function(file) {
                console.log("Fichier supprimé:", file.name);
                uploadedFiles = uploadedFiles.filter(f => f !== file);
            });
            
            document.getElementById('property-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const formData = new FormData(form);
                
                // Ajouter les nouveaux fichiers
                uploadedFiles.forEach(function(file) {
                    formData.append('images[]', file);
                });
                
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mise à jour en cours...';
                
                // Envoyer avec POST (Laravel traitera le _method=PUT)
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data && data.success) {
                        window.location.href = "{{ route('properties.index') }}";
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour de la propriété: ' + error.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Mettre à jour';
                });
            });
        }
    });
</script>
@endpush