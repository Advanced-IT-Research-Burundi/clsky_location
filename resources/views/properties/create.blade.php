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

        init: function () {

            this.on("addedfile", function (file) {
                uploadedFiles.push(file);
            });

            this.on("removedfile", function (file) {
                uploadedFiles = uploadedFiles.filter(f => f !== file);
            });

            document.getElementById('property-form').addEventListener('submit', function (e) {

                e.preventDefault();

                const form = this;
                const formData = new FormData(form);

                // Ajouter images dropzone
                uploadedFiles.forEach(file => {
                    formData.append('images[]', file);
                });

                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {

                    // Si Laravel retourne validation error
                    if (response.status === 422) {
                        const data = await response.json();
                        throw new Error(Object.values(data.errors).flat().join("\n"));
                    }

                    // Si succès → redirection normale Laravel
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }

                    window.location.href = "{{ route('properties.index') }}";
                })
                .catch(error => {

                    alert(error.message);

                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        '<i class="bi bi-save"></i> Créer la propriété';
                });

            });
        }
    });
</script>

@endpush