@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>

        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width:150px;">
                <option>Toutes les périodes</option>
                <option>Aujourd'hui</option>
                <option>Cette semaine</option>
                <option>Ce mois</option>
            </select>

            <button class="btn btn-sm btn-primary">
                <i class="bi bi-arrow-clockwise"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">

        <!-- Total propriétés -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Propriétés</h6>
                        <h3>{{ $properties->count() }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-building text-primary fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Revenus Totaux</h6>
                        <h3>{{ number_format($tousTotalRevenue,2) }} USD</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-currency-dollar text-success fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupation globale -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Taux d'Occupation</h6>

                        @php
                            $occupation = $properties->avg(function($p){
                                $totalDays = max(now()->diffInDays($p->created_at),1);

                                $reserved = $p->reservations->sum(function($r){
                                    return $r->check_in && $r->check_out
                                        ? $r->check_in->diffInDays($r->check_out)
                                        : 0;
                                });

                                return ($reserved/$totalDays)*100;
                            });
                        @endphp

                        <h3>{{ number_format($occupation,1) }}%</h3>
                    </div>

                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-calendar-check text-warning fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Réservations actives -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Réservations Actives</h6>

                        @php
                            $activeReservations = $properties->sum(function($p){
                                return $p->reservations
                                    ->where('status','confirmed')
                                    ->where('check_in','<=',now())
                                    ->where('check_out','>=',now())
                                    ->count();
                            });
                        @endphp

                        <h3>{{ $activeReservations }}</h3>
                    </div>

                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-people text-info fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col">
                    <h5>Détails des Propriétés</h5>
                </div>

                <div class="col-auto">
                    <input id="tableSearch" class="form-control form-control-sm" placeholder="Rechercher">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Propriété</th>
                        <th>Revenus</th>
                        <th>Occupation</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($properties as $property)

                    @php
                        $totalRevenue = $property->reservations
                            ->flatMap->payments
                            ->sum('amount');

                        $primaryImage = $property->images
                            ->where('is_primary',true)
                            ->first();
                    @endphp

                    <tr>

                        <!-- Property -->
                        <td>
                            <div class="d-flex align-items-center">

                                @if($primaryImage)
                                    <img src="{{ asset($primaryImage->image_path) }}"
                                         width="40"
                                         height="40"
                                         class="rounded"
                                         style="object-fit:cover;">
                                @else
                                    <div class="bg-light rounded d-flex justify-content-center align-items-center"
                                         style="width:40px;height:40px;">
                                        <i class="bi bi-house"></i>
                                    </div>
                                @endif

                                <div class="ms-3">
                                    <h6 class="mb-0">{{ $property->title }}</h6>
                                    <small class="text-muted">{{ $property->city }}</small>
                                </div>
                            </div>
                        </td>

                        <!-- Revenue -->
                        <td>
                            {{ number_format($totalRevenue,2) }} USD
                        </td>

                        <!-- Occupancy -->
                        <td>
                            @php
                                $totalDays = max(now()->diffInDays($property->created_at),1);

                                $reservedDays = $property->reservations->sum(function($r){
                                    return $r->check_in && $r->check_out
                                        ? $r->check_in->diffInDays($r->check_out)
                                        : 0;
                                });

                                $rate = ($reservedDays/$totalDays)*100;
                            @endphp

                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" style="height:5px;">
                                    <div class="progress-bar" style="width:{{ $rate }}%"></div>
                                </div>
                                <span class="ms-2">{{ number_format($rate,1) }}%</span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td>
                            @if($property->status === 'available')
                                <span class="badge bg-success">Disponible</span>
                            @elseif($property->status === 'rented')
                                <span class="badge bg-warning">Loué</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($property->status) }}</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td>
                            <a href="{{ route('properties.show',$property) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>

                    </tr>

                @endforeach

                </tbody>
            </table>
        </div>
    </div>

</div>


@push('scripts')
<script>
const searchInput = document.getElementById('tableSearch');

if(searchInput){
    searchInput.addEventListener('keyup', function(){
        let val = this.value.toLowerCase();
        document.querySelectorAll("tbody tr").forEach(row=>{
            row.style.display =
                row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
}
</script>
@endpush

@endsection
