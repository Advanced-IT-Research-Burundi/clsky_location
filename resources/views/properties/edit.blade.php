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
    const existingImages = @json(
        $property->images->map(fn($img) => [
            'id' => $img->id,
            'name' => basename($img->image_path),
            'url' => asset('storage/'.$img->image_path)
        ])
    );
</script>

<script>
Dropzone.autoDiscover = false;

const uploadedFiles = [];
const removedExistingImages = [];

const dropzone = new Dropzone("#imageDropzone", {
    url: "#",
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 20,
    maxFiles: 20,
    maxFilesize: 5,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    dictDefaultMessage: "Glissez-déposez vos images ici ou cliquez",
    thumbnailWidth: 120,
    thumbnailHeight: 120,

    init: function () {
        const dz = this;

        // 🔹 Charger les images existantes
        if (typeof existingImages !== "undefined") {
            existingImages.forEach(image => {
                const mockFile = {
                    name: image.name,
                    size: 123456,
                    serverId: image.id
                };

                dz.emit("addedfile", mockFile);
                dz.emit("thumbnail", mockFile, image.url);
                dz.emit("complete", mockFile);

                mockFile.previewElement.classList.add("dz-existing");
            });
        }

        // 🔹 Nouvelle image
        dz.on("addedfile", file => {
            if (!file.serverId) {
                uploadedFiles.push(file);
            }
        });

        // 🔹 Suppression image
        dz.on("removedfile", file => {
            if (file.serverId) {
                removedExistingImages.push(file.serverId);
            }
        });

        // 🔹 Soumission formulaire
        document.getElementById("property-form").addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            uploadedFiles.forEach(file => {
                formData.append("images[]", file);
            });

            removedExistingImages.forEach(id => {
                formData.append("removed_images[]", id);
            });

            fetch(this.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(err => {
                alert("Erreur lors de la mise à jour");
                console.error(err);
            });
        });
    }
});
</script>

@endpush