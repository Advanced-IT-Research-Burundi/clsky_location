<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('properties.index') }}" method="GET" id="searchForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select" data-filter>
                        <option value="">Type de bien</option>
                        <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Appartement
                        </option>
                        <option value="studio" {{ request('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                        <option value="duplex" {{ request('type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select" data-filter>
                        <option value="">Statut</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible
                        </option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Loué</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                            Maintenance</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="collapse"
                        data-bs-target="#advancedFilters">
                        <i class="bi bi-funnel"></i> Filtres avancés
                    </button>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100" id="resetFilters">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </button>
                </div>
            </div>

            <div class="collapse mt-3" id="advancedFilters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Prix</label>
                        <div class="input-group">
                            <input type="number" name="min_price" class="form-control" placeholder="Min"
                                value="{{ request('min_price') }}">
                            <span class="input-group-text">-</span>
                            <input type="number" name="max_price" class="form-control" placeholder="Max"
                                value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Surface (m²)</label>
                        <div class="input-group">
                            <input type="number" name="min_area" class="form-control" placeholder="Min"
                                value="{{ request('min_area') }}">
                            <span class="input-group-text">-</span>
                            <input type="number" name="max_area" class="form-control" placeholder="Max"
                                value="{{ request('max_area') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Chambres</label>
                        <select name="bedrooms" class="form-select" data-filter>
                            <option value="">Tous</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>
                                    {{ $i }}+
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Salles de bain</label>
                        <select name="bathrooms" class="form-select" data-filter>
                            <option value="">Tous</option>
                            @for ($i = 1; $i <= 3; $i++)
                                <option value="{{ $i }}"
                                    {{ request('bathrooms') == $i ? 'selected' : '' }}>
                                    {{ $i }}+
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Options</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="furnished" id="furnished"
                                value="1" {{ request('furnished') ? 'checked' : '' }}>
                            <label class="form-check-label" for="furnished">Meublé</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="featured" id="featured"
                                value="1" {{ request('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Mis en avant</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit on select change
            document.querySelectorAll('[data-filter]').forEach(filter => {
                filter.addEventListener('change', () => document.getElementById('searchForm').submit());
            });

            // Reset filters
            document.getElementById('resetFilters').addEventListener('click', function() {
                window.location.href = '{{ route('properties.index') }}';
            });

            // Range sliders (si vous voulez ajouter des sliders pour prix et surface)
            if (typeof noUiSlider !== 'undefined') {
                // Configuration des sliders...
            }
        });
    </script>
@endpush
