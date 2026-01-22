@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Historique des paiements</h2>
        </div>
        <div class="col-md-4">
            <!-- Filtres -->
            <form action="{{ route('client.payments.index') }}" method="GET" class="d-flex">
                <select name="status" class="form-select me-2">
                    <option value="">Tous les statuts</option>
                    @foreach(['pending' => 'En attente', 'completed' => 'Complété', 'failed' => 'Échoué', 'refunded' => 'Remboursé'] as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </form>
        </div>
    </div>

    <!-- Résumé des paiements -->
    <div class="row mb-4 g-4">

    <!-- Total payé -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 bg-primary text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-uppercase fw-semibold mb-2">Total payé</h6>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }} USD
                    </h3>
                </div>
                <div class="icon-circle bg-white text-primary">
                    <i data-lucide="wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En attente -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 bg-warning text-dark h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-uppercase fw-semibold mb-2">En attente</h6>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($payments->where('status', 'pending')->sum('amount'), 2) }} USD
                    </h3>
                </div>
                <div class="icon-circle bg-dark text-warning">
                    <i data-lucide="clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Nombre de transactions -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 bg-info text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-uppercase fw-semibold mb-2">Transactions</h6>
                    <h3 class="fw-bold mb-0">{{ $payments->count() }}</h3>
                </div>
                <div class="icon-circle bg-white text-info">
                    <i data-lucide="repeat"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernière transaction -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 bg-success text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title text-uppercase fw-semibold mb-2">Dernière transaction</h6>
                    <h3 class="fw-bold mb-0">
                        {{ $payments->first() ? $payments->first()->created_at->format('d/m/Y') : 'N/A' }}
                    </h3>
                </div>
                <div class="icon-circle bg-white text-success">
                    <i data-lucide="calendar-check"></i>
                </div>
            </div>
        </div>
    </div>

</div>


    <!-- Liste des paiements -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Réservation</th>
                            <th>Méthode</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('client.reservations.show', $payment->reservation_id) }}"
                                       class="text-decoration-none">
                                        {{ $payment->reservation->property->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($payment->payment_method === 'bank_transfer')
                                        <i class="bi bi-bank"></i> Bank Transfer
                                    @elseif($payment->payment_method === 'card')
                                        <i class="bi bi-phone"></i> Card Transfer
                                    @elseif($payment->payment_method === 'cash')
                                        <i class="bi bi-money"></i> Cash Transfer
                                    @endif
                                </td>
                                <td>{{ number_format($payment->amount, 2) }} USD</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status_color }}">
                                        {{ $payment->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('client.payments.show', $payment) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Détails
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-credit-card display-4 text-muted mb-3"></i>
                                        <h5>Aucun paiement trouvé</h5>
                                        <p class="text-muted">Vous n'avez pas encore effectué de paiement.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 8px;
    margin-bottom: 1rem;
}
.table th {
    border-top: none;
    background-color: #f8f9fa;
}
.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}
.bi {
    margin-right: 0.25rem;
}
.icon-circle {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-circle svg {
    width: 26px;
    height: 26px;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

</style>
@endsection
