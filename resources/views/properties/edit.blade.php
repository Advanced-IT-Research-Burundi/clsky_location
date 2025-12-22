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

    {{-- Formulaire principal --}}
    <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data" id="property-form">
        @csrf
        @method('PUT')

        @include('properties._form', ['property' => $property])

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('properties.index') }}" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Mettre à jour
                </button>
            </div>
        </div>
    </form>

    {{-- Images existantes --}}
    @if ($property->images->count())
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Images existantes</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach ($property->images as $image)
                <div class="col-md-2 position-relative">
                    <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail">

                    <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-1">
                        {{-- Supprimer --}}
                        <form action="{{ route('properties.delete-image', $image->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette image ?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                        {{-- Image principale --}}
                        @if (!$image->is_primary)
                        <form action="{{ route('properties.set-primary-image', $image->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-primary btn-sm mt-1">
                                <i class="bi bi-star"></i>
                            </button>
                        </form>
                        @endif
                    </div>

                    @if ($image->is_primary)
                    <span class="badge bg-primary position-absolute bottom-0 start-0 m-2">Principale</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Ajouter de nouvelles images avec Dropzone --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Ajouter de nouvelles images</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('properties.update', $property->id) }}" class="dropzone" id="propertyDropzone">
                @csrf
                @method('PUT')
                <div class="dz-message">
                    Glissez-déposez vos images ici ou cliquez pour sélectionner
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"></script>
<script>
Dropzone.autoDiscover = false;

const dropzone = new Dropzone("#propertyDropzone", {
    paramName: "images",
    maxFilesize: 2, // MB
    maxFiles: 10,
    uploadMultiple: true,
    parallelUploads: 5,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    autoProcessQueue: true,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    init: function () {
        this.on("success", function(file, response) {
            console.log("Image uploadée", response);
        });
        this.on("error", function(file, response) {
            console.log("Erreur upload", response);
        });
    }
});
</script>
@endpush
