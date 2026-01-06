@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vue d'ensemble financière</h1>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm">
                <option value="all_time">Toutes les périodes</option>
                <option value="this_month">Ce mois</option>
                <option value="last_month">Mois dernier</option>
                <option value="this_year">Cette année</option>
            </select>
        </div>
    </div>

    <!-- Cartes de statistiques principales -->
    <div class="row g-4 mb-4">
        <!-- Revenu total -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-primary bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-euro fs-3"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Voir les détails</a></li>
                                <li><a class="dropdown-item" href="#">Exporter</a></li>
                            </ul>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2">Revenu Total</h6>
                    <h3 class="mb-0">{{ number_format($totalAmount, 2) }} €</h3>
                    <div class="mt-3 small">
                        <span class="text-success">
                            <i class="bi bi-arrow-up"></i> +{{ $totalAmount>0 ? number_format(($completedAmount/$totalAmount) * 100, 1): 0 }}%
                        </span>
                        <span class="text-muted ms-2">vs période précédente</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Réservations en attente -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-warning bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2">En Attente</h6>
                    <h3 class="mb-0">{{ number_format($pendingAmount, 2) }} BIF</h3>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-warning"
                             style="width: {{ $totalAmount>0 ? (($pendingAmount/$totalAmount) * 100): 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paiements complétés -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-success bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle fs-3"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2">Complétés</h6>
                    <h3 class="mb-0">{{ number_format($completedAmount, 2) }} BIF</h3>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-success"
                             style="width: {{ $totalAmount>0?(($completedAmount/$totalAmount) * 100): 0  }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remboursements -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-danger bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-arrow-return-left fs-3"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-2">Remboursements</h6>
                    <h3 class="mb-0">{{ number_format($refundedAmount, 2) }} BIF</h3>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-danger"
                             style="width: {{ $totalAmount?(($refundedAmount/$totalAmount) * 100): 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques secondaires -->
    <div class="row g-4 mb-4">
        <!-- Nombre total de réservations -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-check text-info fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Réservations</h6>
                            <h4 class="mb-0">{{ $totalReservations }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prix moyen -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-tag text-success fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Prix Moyen</h6>
                            <h4 class="mb-0">{{ number_format($averagePrice, 2) }} BIF</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Occupation moyenne -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-purple bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-purple fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Occupation Moyenne</h6>
                            <h4 class="mb-0">{{ number_format($averageOccupancy, 1) }} personnes</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des revenus (à implémenter avec Chart.js) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Évolution des revenus</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <!-- Liste des propriétés par revenu -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Propriétés par revenu</h5>
                <button class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download me-1"></i> Exporter
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Propriété</th>
                            <th class="border-0">Revenu Total</th>
                            <th class="border-0">Réservations</th>
                            <th class="border-0">Taux d'occupation</th>
                            <th class="border-0">Statut des paiements</th>
                            <th class="border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        @php
                            $propertyRevenue = $property->reservations->sum('total_paid');
                            $completedPayments = $property->reservations->where('status', 'completed')->sum('total_paid');
                            $pendingPayments = $property->reservations->where('status', 'pending')->sum('total_paid');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($property->images->isNotEmpty())
                                        <img src="{{ Storage::url($property->images->first()->image_path) }}"
                                             class="rounded"
                                             width="40"
                                             height="40">
                                    @else
                                        <div class="bg-light rounded p-2">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    @endif
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ $property->title }}</h6>
                                        <small class="text-muted">{{ $property->address }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0">{{ number_format($propertyRevenue, 2) }} BIF</h6>
                                <div class="small text-success">
                                    <i class="bi bi-arrow-up"></i>
                                    {{ $totalAmount >0 ?number_format(($propertyRevenue/$totalAmount) * 100, 1) : 0}}%
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0">{{ $property->reservations->count() }}</h6>
                                <small class="text-muted">réservations</small>
                            </td>
                            <td>
                                @php
                                    $occupancyRate = $property->reservations->count() > 0
                                        ? ($property->reservations->sum('guests') / $property->reservations->count())
                                        : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 5px;">
                                        <div class="progress-bar bg-success"
                                             style="width: {{ $occupancyRate * 10 }}%"></div>
                                    </div>
                                    <span class="ms-2">{{ number_format($occupancyRate, 1) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($completedPayments > 0)
                                        <span class="badge bg-success">
                                            {{ number_format($completedPayments, 2) }} BIF complétés
                                        </span>
                                    @endif
                                    @if($pendingPayments > 0)
                                        <span class="badge bg-warning">
                                            {{ number_format($pendingPayments, 2) }} BIF en attente
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#propertyDetails{{ $property->id }}">
                                    <i class="bi bi-eye me-1"></i> Détails
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.text-purple {
    color: #6f42c1;
}
.bg-purple {
    background-color: #6f42c1;
}
.progress {
    background-color: #edf2f9;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Configuration du graphique
document.addEventListener('DOMContentLoaded', function() {
    // Configuration du graphique principal
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = @json($chartData);

    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets.map(dataset => ({
                ...dataset,
                tension: 0.4,
                fill: true
            }))
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'EUR'
                            }).format(context.parsed.y);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                style: 'currency',
                                currency: 'EUR',
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    },
                    grid: {
                        drawBorder: false,
                        color: '#e9ecef'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gestionnaire de filtre de période
    const periodSelect = document.querySelector('select[name="period"]');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('period', this.value);
            window.location = url;
        });
    }

    // Formatage des nombres
    document.querySelectorAll('.format-number').forEach(element => {
        const value = parseFloat(element.textContent);
        element.textContent = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    });

    // Animation des progress bars
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
            bar.style.transition = 'width 1s ease-in-out';
        }, 100);
    });

    // Gestionnaire d'export
    document.querySelector('#exportButton')?.addEventListener('click', function() {
        const period = periodSelect?.value || 'all_time';
        window.location.href = `/admin/dashboard/export?period=${period}`;
    });

    // Système de recherche en temps réel
    const searchInput = document.querySelector('#propertySearch');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }, 300));
    }

    // Fonction utilitaire debounce
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endpush
