@extends('layouts.admin')

@section('title', 'Détails de la Propriété')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 text-gray-800">{{ $property->title }}</h1>
        <a href="{{ route('properties.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <div class="row">
        <!-- Images -->
        <div class="col-md-6 mb-4">
            @if($property->images->count())
                <div class="card">
                    <div class="card-body p-0">
                        @foreach($property->images as $image)
                            <img src="{{ Storage::url($image->image_path) }}" class="img-fluid mb-2" style="width:100%; height:200px; object-fit:cover;">
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

        <!-- Infos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>Informations</h5>
                    <p><i class="bi bi-geo-alt"></i> {{ $property->address }}</p>
                    <p><i class="bi bi-door-open"></i> {{ $property->bedrooms }} chambres</p>
                    <p><i class="bi bi-droplet"></i> {{ $property->bathrooms }} salles de bain</p>
                    <p><i class="bi bi-arrows-angle-expand"></i> {{ $property->area }} m²</p>
                    <p><i class="bi bi-cash-stack"></i> {{ number_format($property->price) }} FR</p>
                    <p><span class="badge bg-info">{{ $property->type_text }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services liés -->
    <div class="mb-4">
        <h4>Services associés</h4>
        @if($property->services->count())
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($property->services as $service)
                    <div class="col">
                        <div class="card h-100">
                            @if($service->image)
                                <img src="{{ Storage::url($service->image) }}" class="card-img-top" style="height:150px; object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height:150px;">
                                    <i class="bi bi-gear fs-1 text-gray-400"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $service->name }}</h5>
                                <p class="card-text">{{ Str::limit($service->description, 80) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                Aucun service associé à cette propriété
            </div>
        @endif
    </div>
</div>
@endsection
