@extends('layouts.app')

@section('title', $property->title)

@section('content')
    <div class="container py-5">
        {{-- alert message d'information succes or error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <div class="row">
            <!-- Galerie d'images -->
            <div class="col-lg-8 mb-4">
                <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($property->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ Storage::url($image->image_path) }}" class="d-block w-100"
                                    style="height: 500px; object-fit: cover;" alt="{{ $property->title }}">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>

            <!-- Formulaire de réservation -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="mb-4">{{ number_format($property->price, 0, ',', ' ') }} USD <small
                                class="text-muted">/ nuit</small></h3>

                        <form action="{{ route('client.properties.reserve', $property) }}" method="POST"
                            id="reservationForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Dates</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="check_in" id="check_in"
                                        min="{{ date('Y-m-d') }}" required>
                                    <span class="input-group-text">au</span>
                                    <input type="date" class="form-control" name="check_out" id="check_out" required>
                                </div>
                                {{-- alert input error validation --}}
                                @error('check_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('check_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre d'invités</label>
                                <input type="number" class="form-control" name="guests" min="1" value="1"
                                    required>
                            </div>
                            <!-- alert input error validation --}} -->
                            @error('guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="mb-3">
                                <label class="form-label">Notes (optionnel)</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Nombre de nuits</span>
                                        <strong id="totalNights">--</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span>Prix total</span>
                                        <strong id="totalPrice">--</strong>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    Réserver
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Détails de la propriété -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">{{ $property->title }}</h2>

                        <div class="d-flex mb-4">
                            <div class="me-4">
                                <i class="bi bi-door-open"></i>
                                <span>{{ $property->bedrooms }} chambres</span>
                            </div>
                            <div class="me-4">
                                <i class="bi bi-droplet"></i>
                                <span>{{ $property->bathrooms }} sdb</span>
                            </div>
                            <div>
                                <i class="bi bi-rulers"></i>
                                <span>{{ $property->area }} m²</span>
                            </div>
                        </div>

                        <h5>Description</h5>
                        <p class="mb-4">{{ $property->description }}</p>

                        {{-- <h5>Équipements</h5>
                    <div class="row g-3 mb-4">
                        @foreach ($property->amenities as $amenity)
                            <div class="col-md-4">
                                <i class="bi {{ $amenity->icon }}"></i>
                                {{ $amenity->name }}
                            </div>
                        @endforeach
                    </div> --}}

                        <h5>Localisation</h5>
                        <p>
                            <i class="bi bi-geo-alt"></i>
                            {{ $property->address }}, {{ $property->city }}, {{ $property->country }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const totalPriceEl = document.getElementById('totalPrice');
    const totalNightsEl = document.getElementById('totalNights');

    const pricePerNight = {{ $property->price }};

    function calculateReservation() {
        if (!checkInInput.value || !checkOutInput.value) {
            totalPriceEl.textContent = '--';
            totalNightsEl.textContent = '--';
            return;
        }

        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);

        const diffTime = checkOut - checkIn;
        const nights = diffTime / (1000 * 60 * 60 * 24);

        if (nights > 0) {
            const total = nights * pricePerNight;

            totalNightsEl.textContent = nights + ' nuit(s)';
            totalPriceEl.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' USD';
        } else {
            totalNightsEl.textContent = '--';
            totalPriceEl.textContent = '--';
        }
    }

    checkInInput.addEventListener('change', function () {
        const minCheckOut = new Date(this.value);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        checkOutInput.min = minCheckOut.toISOString().split('T')[0];
        calculateReservation();
    });

    checkOutInput.addEventListener('change', calculateReservation);
});
</script>
@endsection
