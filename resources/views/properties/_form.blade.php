<div class="row g-4">
    {{-- Informations de base --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informations principales</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Titre --}}
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $property->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4" required>{{ old('description', $property->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Type et Statut --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type de bien</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="apartment" {{ old('type', $property->type ?? '') == 'apartment' ? 'selected' : '' }}>Appartement</option>
                                <option value="studio" {{ old('type', $property->type ?? '') == 'studio' ? 'selected' : '' }}>Studio</option>
                                <option value="duplex" {{ old('type', $property->type ?? '') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="available" {{ old('status', $property->status ?? '') == 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="rented" {{ old('status', $property->status ?? '') == 'rented' ? 'selected' : '' }}>Loué</option>
                                <option value="maintenance" {{ old('status', $property->status ?? '') == 'maintenance' ? 'selected' : '' }}>En maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Prix et Surface --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="price" class="form-label">Prix</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $property->price ?? '') }}" 
                                       required>
                                <span class="input-group-text">BIF</span>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="area" class="form-label">Surface</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('area') is-invalid @enderror"
                                    id="area" name="area" value="{{ old('area', $property->area ?? '') }}" required>
                                <span class="input-group-text">m²</span>
                                @error('area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Chambres et Salles de bain --}}
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bedrooms" class="form-label">Chambres</label>
                            <input type="number" class="form-control @error('bedrooms') is-invalid @enderror"
                                id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms ?? '') }}" required>
                            @error('bedrooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bathrooms" class="form-label">Salles de bain</label>
                            <input type="number" class="form-control @error('bathrooms') is-invalid @enderror"
                                id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms ?? '') }}" required>
                            @error('bathrooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="floor" class="form-label">Étage</label>
                            <input type="number" class="form-control @error('floor') is-invalid @enderror"
                                id="floor" name="floor" value="{{ old('floor', $property->floor ?? '') }}">
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informations complémentaires --}}
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Options</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="furnished" name="furnished" value="1"
                        {{ old('furnished', $property->furnished ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="furnished">Meublé</label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                        {{ old('featured', $property->featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="featured">Mise en avant</label>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Localisation</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="address" class="form-label">Adresse</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                        name="address" value="{{ old('address', $property->address ?? '') }}" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="city" class="form-label">Ville</label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                        name="city" value="{{ old('city', $property->city ?? '') }}" required>
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="postal_code" class="form-label">Code postal</label>
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                        id="postal_code" name="postal_code" value="{{ old('postal_code', $property->postal_code ?? '') }}" required>
                    @error('postal_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="country" class="form-label">Pays</label>
                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country"
                        name="country" value="{{ old('country', $property->country ?? 'France') }}" required>
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Services --}}
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Services disponibles</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach ($services as $service)
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    id="service_{{ $service->id }}" value="{{ $service->id }}"
                                    @checked(in_array($service->id, old('services', isset($property) ? $property->services->pluck('id')->toArray() : [])))>
                                <label class="form-check-label" for="service_{{ $service->id }}">
                                    {{ $service->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Gestion des images --}}
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Ajouter des images</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                        id="images" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Prévisualisation des nouvelles images --}}
                <div id="image-preview" class="row g-2 mb-3"></div>

                {{-- Images existantes (édition uniquement) --}}
                @if (isset($property) && $property->images->count() > 0)
                    <div class="row g-2 mb-3">
                        @foreach ($property->images as $image)
                            <div class="col-md-2 position-relative">
                                <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail" alt="Image">
                                <div class="position-absolute top-0 end-0 p-1 d-flex flex-column gap-1">
                                    <form action="{{ route('properties.delete-image', $image->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette image ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
                                    <span class="badge bg-primary position-absolute bottom-0 start-0 m-1">Principal</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        if (input.files) {
            [...input.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'col-md-2';
                    div.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" alt="Prévisualisation">`;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endpush
