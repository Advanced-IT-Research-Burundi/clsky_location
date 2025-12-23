@extends('layouts.admin')

@section('title', 'Gestion des Dépenses')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des Dépenses</h1>
        <a href="{{ route('depenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nouvelle Dépense
        </a>
    </div>

    <!-- Liste des dépenses -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Montant</th>
                            <th>Justificatif</th>
                            <th>Date</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses ?? [] as $depense)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $depense->titre }}</td>
                                <td>{{ number_format($depense->montant, 2) }} €</td>
                                <td>
                                    <a href="{{ asset('storage/' . $depense->justificatif) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        Voir Justificatif
                                    </a>
                                </td>
                                <td>{{ $depense->date_depense  }}</td>
                                <td>{{ $depense->categorie }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $depense->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $depense->id }}" action="{{ route('depenses.destroy', $depense) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="mt-2 mb-0">Aucune dépense trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($depenses) && $depenses->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $depenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
