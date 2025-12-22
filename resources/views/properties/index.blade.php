@extends('layouts.admin')

@section('title', 'Gestion des Propriétés')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Gestion des Propriétés</h1>
        <a href="{{ route('properties.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvelle Propriété
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('properties.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Rechercher..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Type de bien</option>
                        <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Appartement</option>
                        <option value="studio" {{ request('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                        <option value="duplex" {{ request('type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Statut</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Loué</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" 
                           name="min_price" 
                           class="form-control" 
                           placeholder="Prix min" 
                           value="{{ request('min_price') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" 
                           name="max_price" 
                           class="form-control" 
                           placeholder="Prix max" 
                           value="{{ request('max_price') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des propriétés -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($properties as $property)
        <div class="col">
            <div class="card h-100">
                <!-- Image -->
                <div class="position-relative">
                    @if($property->images->where('is_primary', true)->first())
                        <img src="{{ Storage::url($property->images->where('is_primary', true)->first()->image_path) }}" 
                             class="card-img-top" 
                             alt="{{ $property->title }}"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="bi bi-building fs-1 text-gray-400"></i>
                        </div>
                    @endif
                    <span class="position-absolute top-0 end-0 badge bg-{{ $property->status_color }} m-2">
                        {{ $property->status_text }}
                    </span>
                </div>

                <!-- Corps de la carte -->
                <div class="card-body">
                    <h5 class="card-title">{{ $property->title }}</h5>
                    <p class="card-text text-gray-600 mb-2">
                        <i class="bi bi-geo-alt"></i> {{ $property->city }}
                    </p>

                    <!-- Caractéristiques -->
                    <div class="d-flex justify-content-between text-gray-600 mb-3">
                        <span><i class="bi bi-door-open"></i> {{ $property->bedrooms }} ch.</span>
                        <span><i class="bi bi-droplet"></i> {{ $property->bathrooms }} sdb.</span>
                        <span><i class="bi bi-arrows-angle-expand"></i> {{ $property->area }}m²</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-primary">{{ number_format($property->price) }} FR</span>
                        <span class="badge bg-info">{{ $property->type_text }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-footer bg-white border-top-0">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group">
                            <a href="{{ route('properties.edit', $property) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDelete({{ $property->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <a href="{{ route('properties.show', $property) }}" 
                           class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i> Détails
                        </a>
                    </div>

                    <form id="delete-form-{{ $property->id }}" 
                          action="{{ route('properties.destroy', $property) }}" 
                          method="POST" 
                          class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Aucune propriété trouvée
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $properties->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette propriété ?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection