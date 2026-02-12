@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vue d'ensemble financière</h1>
        <select class="form-select form-select-sm w-auto">
            <option value="all_time">Toutes les périodes</option>
            <option value="this_month">Ce mois</option>
            <option value="last_month">Mois dernier</option>
            <option value="this_year">Cette année</option>
        </select>
    </div>

    <!-- Statistiques principales -->
    <div class="row g-4 mb-4">

        <!-- Revenu total -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-dollar fs-3 text-primary"></i>
                        </div>
                    </div>
                    <h6 class="text-muted">Revenu Total</h6>
                    <h3>${{ number_format($totalAmount, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- En attente -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="bg-warning bg-opacity-10 p-3 rounded mb-3">
                        <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                    <h6 class="text-muted">En attente</h6>
                    <h3>${{ number_format($pendingAmount, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Complétés -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="bg-success bg-opacity-10 p-3 rounded mb-3">
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                    <h6 class="text-muted">Complétés</h6>
                    <h3>${{ number_format($completedAmount, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Remboursements -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="bg-danger bg-opacity-10 p-3 rounded mb-3">
                        <i class="bi bi-arrow-return-left fs-3 text-danger"></i>
                    </div>
                    <h6 class="text-muted">Remboursements</h6>
                    <h3>${{ number_format($refundedAmount, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Évolution des revenus</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const chartData = @json($chartData);
    const ctx = document.getElementById('revenueChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets.map(ds => ({
                ...ds,
                fill: true,
                tension: 0.4
            }))
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD'
                        }).format(value)
                    }
                }
            }
        }
    });

});
</script>
@endpush
