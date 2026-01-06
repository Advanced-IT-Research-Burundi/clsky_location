
@extends('layouts.admin')

@section('title', 'Gestion des paiements')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des paiements</h1>
        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouveau paiement
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('payments.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Rechercher</label>
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="ID transaction, Client..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mode de paiement</label>
                    <select class="form-select" name="payment_method">
                        <option value="">Tous</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Carte</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date"
                           class="form-control"
                           name="start_date"
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date"
                           class="form-control"
                           name="end_date"
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Paiements</h6>
                            <h3 class="mb-0">{{ number_format($totalAmount ?? 0, 2) }} BIF</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">En attente</h6>
                            <h3 class="mb-0">{{ number_format($pendingAmount ?? 0, 2) }} BIF</h3>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-hourglass-split fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Complétés</h6>
                            <h3 class="mb-0">{{ number_format($completedAmount ?? 0, 2) }} BIF</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Remboursés</h6>
                            <h3 class="mb-0">{{ number_format($refundedAmount ?? 0, 2) }} BIF</h3>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-arrow-repeat fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des paiements -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Réservation</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments ?? [] as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('reservations.show', $payment->reservation_id) }}">
                                        #{{ $payment->reservation_id }}
                                    </a>
                                </td>
                                <td>{{ $payment->user->name }}</td>
                                <td>{{ number_format($payment->amount, 2) }} BIF</td>
                                <td>
                                    @switch($payment->payment_method)
                                        @case('card')
                                            <span class="badge bg-primary">Carte</span>
                                            @break
                                        @case('bank_transfer')
                                            <span class="badge bg-info">Virement</span>
                                            @break
                                        @case('cash')
                                            <span class="badge bg-success">Espèces</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @switch($payment->status)
                                        @case('pending')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Complété</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger">Échoué</span>
                                            @break
                                        @case('refunded')
                                            <span class="badge bg-info">Remboursé</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('payments.show', $payment) }}"
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $payment->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="mt-2 mb-0">Aucun paiement trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($payments) && $payments->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
