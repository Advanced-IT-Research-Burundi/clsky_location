@extends('layouts.admin')

@section('title', 'Créer un service')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Nouveau service
                </h5>
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Nom -->
                    <div class="mb-3">
                        <label class="form-label">Nom du service</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description') }}</textarea>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label">Image (optionnel)</label>
                        <input type="file"
                               name="image"
                               class="form-control"
                               accept="image/*">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
