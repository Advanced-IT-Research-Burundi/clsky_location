
@extends('layouts.admin')

@section('title', 'Modifier le service')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Modifier le service
                </h5>
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('services.update', $service) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Nom -->
                    <div class="mb-3">
                        <label class="form-label">Nom du service</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $service->name) }}"
                               required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file"
                               name="image"
                               class="form-control"
                               accept="image/*">
                    </div>

                    @if($service->image)
                        <div class="mb-3">
                            <label class="form-label">Image actuelle</label><br>
                            <img src="{{ asset('storage/'.$service->image) }}"
                                 alt="Service image"
                                 class="img-thumbnail"
                                 width="200">
                        </div>
                    @endif

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
