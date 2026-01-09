@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Messages</h1>
        <div>
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <a href="{{ route('archived') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-archive"></i> Messages archivés
                    </a>
                    <a href="{{ route('messages.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Nouveau message
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <!-- Liste des messages -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Statut</th>
                                    <th>De</th>
                                    <th>À</th>
                                    <th>Sujet</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr @if(!$message->read_at && $message->receiver_id === auth()->id()) class="table-active" @endif>
                                        <td>
                                            @if(!$message->read_at && $message->receiver_id === auth()->id())
                                                <span class="badge bg-primary">Non lu</span>
                                            @else
                                                <span class="badge bg-secondary">Lu</span>
                                            @endif
                                        </td>
                                        <td>{{ $message->sender->name }}</td>
                                        <td>{{ $message->receiver->name }}</td>
                                        <td>
                                            <a href="{{ route('messages.show', $message) }}"
                                               class="text-decoration-none">
                                                {{ $message->subject }}
                                            </a>
                                        </td>
                                        <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('messages.show', $message) }}"
                                                   class="btn btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        onclick="confirmDelete({{ $message->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $message->id }}"
                                                  action="{{ route('messages.destroy', $message) }}"
                                                  method="POST"
                                                  class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-envelope-x fs-1 text-muted"></i>
                                            <p class="mt-2 mb-0">Aucun message</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
