
{{-- resources/views/properties/show.blade.php --}}
@extends('layouts.admin')

@section('title', $property->title . ' - Détails')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $property->title }}</h1>
            <div class="mt-2">
                <span class="badge bg-{{ $property->status_color }}">{{ $property->status_text }}</span>
                <span class="badge bg-info ms-2">{{ $property->type_text }}</span>
                @if($property->featured)
                    <span class="badge bg-warning ms-2">
                        <i class="bi bi-star-fill"></i> Mise en avant
                    </span>
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('properties.edit', $property) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Galerie d'images -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    @if($property->images->count() > 0)
                        <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($property->images as $key => $image)
                                    <button type="button" 
                                            data-bs-target="#propertyCarousel" 
                                            data-bs-slide-to="{{ $key }}" 
                                            class="{{ $key === 0 ? 'active' : '' }}"
                                            aria-label="Slide {{ $key + 1 }}">
                                    </button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($property->images as $key => $image)
                                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::url($image->image_path) }}" 
                                             class="d-block w-100" 
                                             alt="Image {{ $key + 1 }}"
                                             style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        </div>
                        <!-- Miniatures -->
                        <div class="row mt-3 g-2">
                            @foreach($property->images as $image)
                                <div class="col-2">
                                    <img src="{{ Storage::url($image->image_path) }}" 
                                         class="img-thumbnail w-100" 
                                         style="height: 80px; object-fit: cover; cursor: pointer;"
                                         onclick="$('#propertyCarousel').carousel({{ $loop->index }})"
                                         alt="Miniature {{ $loop->iteration }}">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 bg-light">
                            <i class="bi bi-image fs-1 text-gray-400"></i>
                            <p class="mt-2 text-gray-500">Aucune image disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations principales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash fs-4 text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Prix</div>
                                    <div class="fw-bold">{{ number_format($property->price) }} BIF</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-arrows-angle-expand fs-4 text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Surface</div>
                                    <div class="fw-bold">{{ $property->area }} m²</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-door-open fs-4 text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Chambres</div>
                                    <div class="fw-bold">{{ $property->bedrooms }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-droplet fs-4 text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Salles de bain</div>
                                    <div class="fw-bold">{{ $property->bathrooms }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building fs-4 text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Étage</div>
                                    <div class="fw-bold">{{ $property->floor ?? 'RDC' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box-seam fs-4 text-primary me-2"></i>
                                <div>
                                    <div class="small text-muted">Meublé</div>
                                    <div class="fw-bold">
                                        @if($property->furnished)
                                            <span class="text-success">Oui</span>
                                        @else
                                            <span class="text-danger">Non</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Localisation</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <i class="bi bi-geo-alt text-primary me-2"></i>
                        {{ $property->address }}
                    </p>
                    <p class="mb-1">
                        <i class="bi bi-buildings text-primary me-2"></i>
                        {{ $property->postal_code }} {{ $property->city }}
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-globe text-primary me-2"></i>
                        {{ $property->country }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Description</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($property->description)) !!}
                </div>
            </div>
        </div>

        <!-- Réservations -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Réservations</h5>
                    <button type="button" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Nouvelle réservation
                    </button>
                </div>
                <div class="card-body">
                    @if($property->reservations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Date début</th>
                                        <th>Date fin</th>
                                        <th>Statut</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($property->reservations as $reservation)
                                        <tr>
                                            <td>{{ $reservation->user->name }}</td>
                                            <td>{{ $reservation->check_in->format('d/m/Y') }}</td>
                                            <td>{{ $reservation->check_out->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $reservation->status_color }}">
                                                    {{ $reservation->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($reservation->total_price) }} €</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Aucune réservation pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
