<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                    <h1 class="mb-3">Liste de Propriétés</h1>
                    <p>Trouvez la propriété de vos rêves parmi nos annonces soigneusement sélectionnées.</p>
                </div>
            </div>
            <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}" href="{{ request()->url() }}">Tout</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-primary {{ request('status') === 'for_sale' ? 'active' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['status' => 'for_sale']) }}">À Vendre</a>
                    </li>
                    <li class="nav-item me-0">
                        <a class="btn btn-outline-primary {{ request('status') === 'for_rent' ? 'active' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['status' => 'for_rent']) }}">À Louer</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    @forelse($properties as $property)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="property-item rounded overflow-hidden shadow-sm">
                            <!-- Image de la propriété -->
                            <div class="position-relative overflow-hidden">
                                <div id="carousel-{{ $property->id }}" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @forelse($property->images as $key => $image)
                                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                                <a class="d-block h5 mb-2 text-truncate" href="{{ route('client.properties.show', $property->id) }}">
                                                    <img src="{{ Storage::url($image->image_path) }}"
                                                         class="d-block w-100"
                                                         alt="Image de la propriété {{ $key + 1 }}"
                                                         style="height: 300px; object-fit: cover;">
                                                </a>
                                            </div>
                                        @empty
                                            <div class="carousel-item active">
                                                <img src="/img/default-property.jpg"
                                                     class="d-block w-100"
                                                     alt="Image par défaut de la propriété"
                                                     style="height: 300px; object-fit: cover;">
                                            </div>
                                        @endforelse
                                    </div>
                                    @if($property->images->count() > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-{{ $property->id }}" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-{{ $property->id }}" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Badge de statut de propriété -->
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">
                                    {{ $property->status === 'for_sale' ? 'À Vendre' : 'À Louer' }}
                                </div>

                                <!-- Badge de type de propriété -->
                                <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                    {{ ucfirst($property->type) }}
                                </div>
                            </div>

                            <!-- Détails de la propriété -->
                            <div class="p-4 pb-0">
                                <h5 class="text-primary mb-3">{{ number_format($property->price) }} BIF</h5>
                                <a class="d-block h5 mb-2 text-truncate" href="{{ route('client.properties.show', $property->id) }}">
                                    {{ $property->title }}
                                </a>
                                <p class="text-body mb-3">
                                    <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                    {{ $property->address }}, {{ $property->city }}
                                </p>
                            </div>

                            <!-- Caractéristiques de la propriété -->
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2">
                                    <i class="fa fa-ruler-combined text-primary me-2"></i>{{ number_format($property->square_feet) }} m²
                                </small>
                                <small class="flex-fill text-center border-end py-2">
                                    <i class="fa fa-bed text-primary me-2"></i>{{ $property->bedrooms }} Chambre(s)
                                </small>
                                <small class="flex-fill text-center py-2">
                                    <i class="fa fa-bath text-primary me-2"></i>{{ $property->bathrooms }} Salle(s) de Bain
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state wow fadeInUp" data-wow-delay="0.1s">
                            <i class="fas fa-home fa-4x text-primary mb-4"></i>
                            <h3>Aucune Propriété Trouvée</h3>
                            <p class="text-muted">Nous n'avons trouvé aucune propriété correspondant à vos critères.</p>
                            <a href="{{ route('properties.index') }}" class="btn btn-primary mt-3">Effacer les Filtres</a>
                        </div>
                    </div>
                    @endforelse

                    <div class="col-12 text-center">
                        <a class="btn btn-primary py-3 px-5" href="{{ route('client.properties.index') }}">Parcourir Plus de Propriétés</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
