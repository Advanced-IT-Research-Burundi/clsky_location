@extends('layouts.admin')

@section('title', 'Rapports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Rapports & Analyses</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="exportReport('pdf')">
                <i class="bi bi-file-pdf"></i> Exporter PDF
            </button>
            <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                <i class="bi bi-file-excel"></i> Exporter Excel
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>7 derniers jours</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>30 derniers jours</option>
                        <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>3 derniers mois</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>12 derniers mois</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Revenus totaux</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_revenue'], 2) }} USD</h3>
                    <div class="mt-2 small">
                        <i class="bi bi-graph-up text-success"></i>
                        <span class="text-success">+{{ number_format($stats['revenue_growth'] ?? 0, 1) }}%</span>
                        vs période précédente
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Réservations</h6>
                    <h3 class="mb-0">{{ $stats['total_reservations'] }}</h3>
                    <div class="mt-2 small">
                        <i class="bi bi-calendar-check text-primary"></i>
                        Taux de conversion: {{ number_format($stats['conversion_rate'] ?? 0, 1) }}%
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Taux d'occupation</h6>
                    <h3 class="mb-0">{{ number_format($stats['average_occupancy'], 1) }}%</h3>
                    <div class="mt-2 small">
                        <i class="bi bi-house-check text-info"></i>
                        Moyenne sur la période
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Nouveaux clients</h6>
                    <h3 class="mb-0">{{ $stats['new_customers'] }}</h3>
                    <div class="mt-2 small">
                        <i class="bi bi-people text-warning"></i>
                        Sur la période
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique des revenus -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Évolution des revenus</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Répartition par type -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Réservations par type</h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Taux d'occupation par propriété -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Taux d'occupation par propriété</h5>
                </div>
                <div class="card-body">
                    <canvas id="occupancyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Configuration des graphiques
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des revenus
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($charts['revenue_by_month']->pluck('month')) !!},
            datasets: [{
                label: 'Revenus (USD)',
                data: {!! json_encode($charts['revenue_by_month']->pluck('total')) !!},
                borderColor: '#3b82f6',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Graphique des types
    new Chart(document.getElementById('typeChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($charts['reservations_by_type']->pluck('type')) !!},
            datasets: [{
                data: {!! json_encode($charts['reservations_by_type']->pluck('total')) !!},
                backgroundColor: ['#3b82f6', '#ef4444', '#10b981', '#f59e0b']
            }]
        }
    });

    // Graphique d'occupation
    new Chart(document.getElementById('occupancyChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($charts['occupancy_by_property']->pluck('name')) !!},
            datasets: [{
                label: 'Taux d\'occupation (%)',
                data: {!! json_encode($charts['occupancy_by_property']->pluck('rate')) !!},
                backgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});

function exportReport(format) {
    const period = document.querySelector('select[name="period"]').value;
    window.location.href = `{{ route('reports.export') }}?format=${format}&period=${period}`;
}
</script>
@endpush
@endsection
