@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête et cartes statistiques restent les mêmes -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width: 150px;">
                <option value="all">Toutes les périodes</option>
                <option value="today">Aujourd'hui</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
            </select>
            <button class="btn btn-sm btn-primary">
                <i class="bi bi-arrow-clockwise"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Cartes Statistiques -->
    <div class="row g-4 mb-4">
        <!-- Total des propriétés -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Propriétés</h6>
                            <h3 class="mb-0">{{ $properties->count() }}</h3>
                        </div>
                        <div class="text-primary bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                    <div class="text-success mt-2 small">
                        <i class="bi bi-arrow-up"></i>
                        {{ number_format(($properties->count() / max($properties->count(), 1)) * 100, 1) }}% ce mois
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenus totaux -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Revenus Totaux</h6>
                            <h3 class="mb-0">{{ $tousTotalRevenue }} USD</h3>
                        </div>
                        <div class="text-success bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-currency-bif fs-1"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taux d'occupation -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Taux d'Occupation</h6>
                            <h3 class="mb-0">{{ number_format($properties->avg(function($p) {
                                $totalDays = now()->diffInDays($p->created_at);
                                $reservedDays = $p->reservations->sum(function($r) {
                                    return $r->check_in->diffInDays($r->check_out);
                                });
                                return $totalDays > 0 ? ($reservedDays / $totalDays) * 100 : 0;
                            }), 1) }}%</h3>
                        </div>
                        <div class="text-warning bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-calendar-check fs-1"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Réservations en cours -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Réservations Actives</h6>
                            <h3 class="mb-0">{{ $properties->sum(function($p) {
                                return $p->reservations->where('status', 'confirmed')
                                    ->where('check_in', '<=', now())
                                    ->where('check_out', '>=', now())
                                    ->count();
                            }) }}</h3>
                        </div>
                        <div class="text-info bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                    <div class="text-info mt-2 small">
                        <i class="bi bi-arrow-up-right"></i> En temps réel
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Propriétés et Paiements -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Détails des Propriétés et Paiements
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Rechercher...">
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Propriété</th>
                            <th class="border-0">Revenus Totaux</th>
                            <th class="border-0">Derniers Paiements</th>
                            <th class="border-0">Occupation</th>
                            <th class="border-0">Statut</th>
                            <th class="border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        @php
                            $totalRevenue = $property->reservations->flatMap(function ($reservation) {
                                                return $reservation->payments;
                                            })->sum('amount');
                            $latestPayments = $property->reservations->flatMap->payments
                                ->sortByDesc('created_at')
                                ->take(5);
                        @endphp
                        <tr>
                            <td style="min-width: 250px;">
                                <div class="d-flex align-items-center">
                                    @if($property->images->isNotEmpty())
                                        <img src="{{ Storage::url($property->images->first()->image_path) }}"
                                             class="rounded"
                                             width="40"
                                             height="40"
                                             style="object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-house text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ $property->title }}</h6>
                                        <small class="text-muted">{{ $property->city }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="min-width: 150px;">
                                <div class="d-flex flex-column">
                                    <span class="h5 mb-0">{{ number_format($totalRevenue, 2) }} USD</span>
                                    <small class="text-muted">
                                        {{ $property->reservations->count() }} réservation(s)
                                    </small>
                                </div>
                            </td>
                            <td style="min-width: 200px;">
                                <div class="d-flex flex-column gap-1">
                                    @forelse($latestPayments as $payment)
                                        <div class="badge bg-light text-dark text-start">
                                            <i class="bi bi-credit-card me-1"></i>
                                            {{ number_format($payment->amount, 2) }} USD
                                            <small class="text-muted">
                                                ({{ $payment->created_at->format('d/m/Y') }})
                                            </small>
                                        </div>
                                    @empty
                                        <span class="text-muted">Aucun paiement</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="min-width: 150px;">
                                @php
                                    $totalDays = now()->diffInDays($property->created_at);
                                    $reservedDays = $property->reservations->sum(function($r) {
                                        return $r->check_in->diffInDays($r->check_out);
                                    });
                                    $occupancyRate = $totalDays > 0 ? ($reservedDays / $totalDays) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 5px;">
                                        <div class="progress-bar bg-success"
                                             style="width: {{ $occupancyRate }}%"></div>
                                    </div>
                                    <span class="ms-2">{{ number_format($occupancyRate, 1) }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($property->status === 'available')
                                    <span class="badge bg-success">Disponible</span>
                                @elseif($property->status === 'occupied')
                                    <span class="badge bg-warning">Occupé</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($property->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#paymentModal{{ $property->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    {{-- <a href="#" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-graph-up"></i>
                                    </a> --}}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales pour chaque propriété -->
    @foreach($properties as $property)
    <div class="modal fade" id="paymentModal{{ $property->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails des paiements - {{ $property->title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Résumé des paiements -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 bg-primary bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="text-primary mb-2">Total des revenus</h6>
                                    <h3 class="mb-0">{{ number_format($property->reservations->sum('total_paid'), 2) }} USD</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-success bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="text-success mb-2">Réservations payées</h6>
                                    <h3 class="mb-0">{{ $property->reservations->where('payment_status', 'paid')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="text-warning mb-2">En attente</h6>
                                    <h3 class="mb-0">{{ $property->reservations->where('payment_status', 'pending')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des réservations et paiements -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Période</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Méthode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($property->reservations->sortByDesc('check_in') as $reservation)
                                    @foreach($reservation->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle p-2 me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $reservation->user?->name ?? 'Client inconnu'}}</div>
                                                    <small class="text-muted">{{ $reservation->user?->email ?? '—'  }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $reservation->check_in->format('d/m/Y') }}</span>
                                                <span>{{ $reservation->check_out->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($payment->amount, 2) }} USD</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($payment->payment_method === 'card')
                                                    <i class="bi bi-credit-card me-2"></i>
                                                @elseif($payment->payment_method === 'bank_transfer')
                                                    <i class="bi bi-bank me-2"></i>
                                                @else
                                                    <i class="bi bi-cash me-2"></i>
                                                @endif
                                                {{ ucfirst($payment->payment_method) }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
/* Styles existants */
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.progress {
    background-color: #edf2f9;
}
.table > :not(caption) > * > * {
    padding: 1rem;
}
.btn-group > .btn {
    padding: 0.25rem 0.5rem;
}

/* Nouveaux styles pour les modales */
.modal-body {
    max-height: 70vh;
}
.modal .table td {
    vertical-align: middle;
}
@media print {
    .modal {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        padding: 0;
        overflow: visible !important;
    }
}
</style>

@push('scripts')
<script>
// Script pour la recherche dans le tableau
document.querySelector('input[placeholder="Rechercher..."]').addEventListener('keyup', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
