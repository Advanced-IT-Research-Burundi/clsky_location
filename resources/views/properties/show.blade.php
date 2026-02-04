@extends('layouts.admin')

@section('title', 'Détails de la Propriété')

@section('styles')
<style>
    .detail-item {
        border-left: 3px solid #007bff;
        transition: all 0.3s ease;
    }

    .detail-item:hover {
        border-left-color: #0056b3;
        background-color: #f8f9fa !important;
    }

    .detail-item i { font-size: 1.2rem; }

    .detail-item strong {
        font-size: 0.95rem;
        color: #333;
    }

    .detail-item small { color: #6c757d; }

    .badge {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    .avatar-circle { font-weight: bold; }

    @media (max-width: 768px) {
        .detail-item { margin-bottom: 1rem; }
    }

    .card { transition: box-shadow 0.3s ease; }

    .card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h1 class="h3 text-gray-800">{{ $property->title }}</h1>
        <a href="{{ route('properties.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">

        <!-- Images -->
        <div class="col-md-6 mb-4">
            @if($property->images->count())
                <div class="card">
                    <div class="card-body p-0">
                        @foreach($property->images as $image)
                            @if($image->image_path)
                                <img 
                                    src="{{ asset($image->image_path) }}"
                                    class="img-fluid mb-2"
                                    style="width:100%; height:200px; object-fit:cover;"
                                    alt="Image propriété">
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card bg-light text-center p-5">
                    <i class="bi bi-building fs-1 text-gray-400"></i>
                    <p class="text-gray-600 mt-2">Aucune image disponible</p>
                </div>
            @endif
        </div>

        <!-- Infos principales -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">

                    <h5 class="card-title mb-4">Informations & Caractéristiques</h5>

                    <div class="mb-4">
                        <p class="mb-2">
                            <i class="bi bi-geo-alt text-primary"></i>
                            <strong>Adresse :</strong> {{ $property->address ?? 'Non spécifié' }}
                        </p>

                        <p class="mb-2">
                            <i class="bi bi-door-open text-primary"></i>
                            <strong>Chambres :</strong> {{ $property->bedrooms ?? 0 }}
                        </p>

                        <p class="mb-2">
                            <i class="bi bi-droplet text-primary"></i>
                            <strong>Salles de bain :</strong> {{ $property->bathrooms ?? 0 }}
                        </p>

                        <p class="mb-2">
                            <i class="bi bi-arrows-angle-expand text-primary"></i>
                            <strong>Surface :</strong> {{ $property->area ?? 0 }} m²
                        </p>

                        <p class="mb-2">
                            <i class="bi bi-cash-stack text-success"></i>
                            <strong>Prix :</strong>
                            <span class="fw-bold text-success">
                                {{ number_format($property->price, 0, ',', ' ') }} FBU
                            </span>
                        </p>

                        <span class="badge bg-info">
                            {{ $property->type_text ?? $property->type }}
                        </span>

                        <span class="badge bg-{{ $property->status === 'available' ? 'success' : 'warning' }}">
                            {{ ucfirst($property->status ?? 'disponible') }}
                        </span>
                    </div>

                    <!-- Caractéristiques supplémentaires -->
                    @if($property->details->count())
                        <hr>
                        <h6 class="mb-3 text-muted">
                            <i class="bi bi-list-check"></i> Caractéristiques supplémentaires
                        </h6>

                        <div class="row g-3">
                            @foreach($property->details as $detail)
                                <div class="col-md-6">
                                    <div class="detail-item p-3 bg-light rounded h-100">
                                        <strong class="text-muted d-block">
                                            {{ $detail->title }}
                                        </strong>

                                        <small class="d-block">
                                            {{ $detail->value }}
                                        </small>

                                        @if($detail->description)
                                            <small class="text-muted" style="font-size:0.75rem;">
                                                {{ $detail->description }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <!-- Description -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-file-text"></i> Description
                    </h5>
                    <p class="mb-0">
                        {{ $property->description ?? 'Aucune description disponible' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Propriétaire -->
    @if($property->user)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h5 class="card-title mb-3">
                        <i class="bi bi-person-circle"></i> Propriétaire
                    </h5>

                    <div class="d-flex align-items-center">

                        <div class="avatar-circle bg-primary text-white me-3"
                             style="width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                            {{ strtoupper(substr($property->user->name,0,1)) }}
                        </div>

                        <div>
                            <strong class="d-block">{{ $property->user->name }}</strong>
                            <small class="text-muted">{{ $property->user->email }}</small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Services -->
    @if($property->services && $property->services->count())
    <div class="mb-4">
        <div class="card">
            <div class="card-body">

                <h5 class="card-title mb-3">
                    <i class="bi bi-gear"></i> Services associés
                </h5>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($property->services as $service)
                        <div class="col">
                            <div class="card h-100 border">

                                @if($service->image)
                                    <img 
                                        src="{{ asset($service->image) }}"
                                        class="card-img-top"
                                        style="height:150px;object-fit:cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="height:150px;">
                                        <i class="bi bi-gear fs-1 text-gray-400"></i>
                                    </div>
                                @endif

                                <div class="card-body">
                                    <h6 class="card-title">{{ $service->name }}</h6>
                                    <p class="card-text small text-muted">
                                        {{ Str::limit($service->description,80) }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h5 class="card-title mb-3">Actions</h5>

                    <div class="d-flex gap-2">

                        <a href="{{ route('properties.edit',$property) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>

                        <form action="{{ route('properties.destroy',$property) }}"
                              method="POST"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette propriété ?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Supprimer
                            </button>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
.detail-item { transition: all 0.3s ease; }

.detail-item:hover {
    background-color: #e9ecef !important;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media print {
    .btn, .card-header, nav { display:none !important; }
}
</style>
@endsection
