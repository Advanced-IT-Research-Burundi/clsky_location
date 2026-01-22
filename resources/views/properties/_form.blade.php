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
                                id="title" name="title" value="{{ old('title', $property->title ?? '') }}"
                                required>
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
                            <select class="form-select @error('type') is-invalid @enderror" id="type"
                                name="type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="apartment"
                                    {{ old('type', $property->type ?? '') == 'apartment' ? 'selected' : '' }}>
                                    Appartement</option>
                                <option value="studio"
                                    {{ old('type', $property->type ?? '') == 'studio' ? 'selected' : '' }}>Studio
                                </option>
                                <option value="duplex"
                                    {{ old('type', $property->type ?? '') == 'duplex' ? 'selected' : '' }}>Duplex
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                name="status" required>
                                <option value="available"
                                    {{ old('status', $property->status ?? '') == 'available' ? 'selected' : '' }}>
                                    Disponible</option>
                                <option value="rented"
                                    {{ old('status', $property->status ?? '') == 'rented' ? 'selected' : '' }}>Loué
                                </option>
                                <option value="maintenance"
                                    {{ old('status', $property->status ?? '') == 'maintenance' ? 'selected' : '' }}>En
                                    maintenance</option>
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
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    id="price" name="price" value="{{ old('price', $property->price ?? '') }}"
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
                                    id="area" name="area" value="{{ old('area', $property->area ?? '') }}"
                                    required>
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
                                id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms ?? '') }}"
                                required>
                            @error('bedrooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bathrooms" class="form-label">Salles de bain</label>
                            <input type="number" class="form-control @error('bathrooms') is-invalid @enderror"
                                id="bathrooms" name="bathrooms"
                                value="{{ old('bathrooms', $property->bathrooms ?? '') }}" required>
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
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Localisation</h5>
            </div>
            <div class="d-flex card-body row g-2">
                <div class="col-md-3 mb-3">
                    <label for="address" class="form-label">Adresse</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                        name="address" value="{{ old('address', $property->address ?? '') }}" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="city" class="form-label">Ville</label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                        name="city" value="{{ old('city', $property->city ?? '') }}" required>
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="postal_code" class="form-label">Code postal</label>
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                        id="postal_code" name="postal_code"
                        value="{{ old('postal_code', $property->postal_code ?? '') }}" required>
                    @error('postal_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="country" class="form-label">Pays</label>
                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country"
                        name="country" value="{{ old('country', $property->country ?? 'Burundi') }}" required>
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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

        {{-- Caractéristiques supplémentaires --}}
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Caractéristiques</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-detail">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>

                <div class="card-body">
                    <div id="details-wrapper">

                        @error('details')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        @if (old('details'))
                            @foreach (old('details') as $i => $detail)
                                @include('properties.partials.detail_row', [
                                    'i' => $i,
                                    'detail' => (object) $detail,
                                ])
                            @endforeach
                        @elseif(isset($property) && $property->details->count())
                            @foreach ($property->details as $i => $detail)
                                @include('properties.partials.detail_row', compact('i', 'detail'))
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Gestion des images avec Dropzone --}}
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-images"></i>
                    Gestion des images
                    <small class="text-muted">(Maximum 20 images, 5MB par image)</small>
                </h5>
            </div>
            <div class="card-body">
            
                @if (isset($property) && $property->images->count() > 0)
                    <div class="mb-4">
                        <h6 class="mb-3">Images existantes</h6>
                        <div class="row g-3" id="existing-images">
                            @foreach ($property->images as $image)
                                <div class="col-md-2" id="image-{{ $image->id }}">
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail w-100"
                                            style="height: 150px; object-fit: cover;" alt="Image">

                                        <div class="position-absolute top-0 end-0 p-1">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="deleteExistingImage({{ $image->id }})" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <div class="position-absolute top-0 start-0 p-1">
                                            @if (!$image->is_primary)
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="setPrimaryImage({{ $image->id }})"
                                                    title="Définir comme principale">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            @endif
                                        </div>

                                        @if ($image->is_primary)
                                            <span class="badge bg-primary position-absolute bottom-0 start-0 m-1">
                                                <i class="bi bi-star-fill"></i> Principale
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr class="my-4">
                    </div>
                @endif

                <div>
                    <h6 class="mb-3">
                        {{ isset($property) ? 'Ajouter de nouvelles images' : 'Ajouter des images' }}
                    </h6>
                    <div id="imageDropzone" class="dropzone">
                        <div class="dz-message">
                            <i class="bi bi-cloud-upload" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Glissez-déposez vos images ici</h5>
                            <p class="text-muted">ou cliquez pour sélectionner des fichiers</p>
                        </div>
                    </div>
                </div>

                @error('images.*')
                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Fonction pour supprimer une image existante
        function deleteExistingImage(imageId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                return;
            }

            fetch(`/properties/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`image-${imageId}`).remove();
                        // Afficher un message de succès
                        showAlert('Image supprimée avec succès', 'success');
                    } else {
                        showAlert('Erreur lors de la suppression', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showAlert('Erreur lors de la suppression', 'danger');
                });
        }

        // Fonction pour définir une image comme principale
        function setPrimaryImage(imageId) {
            fetch(`/admin/properties/images/${imageId}/set-primary`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour mettre à jour l'affichage
                        location.reload();
                    } else {
                        showAlert('Erreur lors de la modification', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showAlert('Erreur lors de la modification', 'danger');
                });
        }

        // Fonction pour afficher des alertes
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className =
                `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        //caracteristiques supplémentaires
        let detailIndex = document.querySelectorAll('.detail-item').length;

        document.getElementById('add-detail')?.addEventListener('click', function() {
            document.getElementById('details-wrapper').insertAdjacentHTML('beforeend', `
        <div class="row g-2 mb-2 detail-item">
            <div class="col-md-4">
                <input name="details[${detailIndex}][title]" class="form-control" placeholder="Nom" required>
            </div>
            <div class="col-md-4">
                <input name="details[${detailIndex}][value]" class="form-control" placeholder="Valeur" required>
            </div>
            <div class="col-md-3">
                <input name="details[${detailIndex}][description]" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-detail">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `);

            detailIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-detail')) {
                e.target.closest('.detail-item').remove();
            }
        });
    </script>
@endpush
