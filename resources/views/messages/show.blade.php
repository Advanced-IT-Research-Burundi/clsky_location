@extends('layouts.admin')

@section('title', 'Détails du message')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Message</h1>
            <div>
                <button type="button" class="btn btn-primary me-2" onclick="showReplyForm()">
                    <i class="bi bi-reply"></i> Répondre
                </button>
                <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Message principal -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <div>
                            <h5 class="card-title mb-1">{{ $message->subject }}</h5>
                            <small class="text-muted">
                                De:
                                <strong>
                                    @if ($message->sender)
                                        {{ $message->sender->name }}
                                    @else
                                        {{ $message->name ?? 'Visiteur' }} <small>(Contact)</small>
                                    @endif
                                </strong>
                                à:
                                <strong>
                                    {{ $message->receiver?->name ?? 'Admin' }}
                                </strong>
                            </small>
                        </div>
                        <small class="text-muted">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="card-body">
                        @if ($message->property)
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-building me-2"></i>
                                Concernant la propriété:
                                <a href="{{ route('properties.show', $message->property) }}" class="alert-link">
                                    {{ $message->property->title }}
                                </a>
                            </div>
                        @endif

                        <div class="message-content">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if ($message->read_at && $message->receiver_id === auth()->id())
                                    <small class="text-muted">
                                        <i class="bi bi-check2-all"></i>
                                        @if ($message->read_at)
                                            lu le
                                            {{ \Carbon\Carbon::createFromTimestamp($message->read_at)->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de réponse (caché par défaut) -->
                <div class="card mb-4 d-none" id="replyForm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Répondre au message</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('messages.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $message->sender_id }}">
                            <input type="hidden" name="property_id" value="{{ $message->property_id }}">

                            <div class="mb-3">
                                <label for="subject" class="form-label">Sujet</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                    value="Re: {{ $message->subject }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Message</label>
                                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="hideReplyForm()">
                                    Annuler
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Historique des messages liés -->
                @if ($relatedMessages && $relatedMessages->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Messages précédents</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($relatedMessages as $relatedMessage)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $relatedMessage->subject }}</h6>
                                            <small class="text-muted">
                                                De
                                                @if ($relatedMessage->sender)
                                                    {{ $relatedMessage->sender?->name }}
                                                @else
                                                    {{ $relatedMessage->name ?? 'Visiteur' }}
                                                @endif
                                                le {{ $relatedMessage->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <a href="{{ route('messages.show', $relatedMessage) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Informations de l'expéditeur -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Expéditeur</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if ($message->sender?->avatar)
                                <img src="{{ Storage::url($message->sender?->avatar) }}" class="rounded-circle me-3"
                                    width="48" height="48" alt="{{ $message->sender?->name }}">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 48px; height: 48px;">
                                    {{ substr($message->sender?->name ?? 'Visiteur', 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $message->sender?->name ?? 'Visiteur' }}</h6>
                                <small class="text-muted">{{ $message->sender?->role_text ?? 'Visiteur' }}</small>
                            </div>
                        </div>
                        @if ($message->sender?->phone)
                            <p class="mb-2">
                                <i class="bi bi-telephone me-2"></i>
                                {{ $message->sender?->phone }}
                            </p>
                        @endif
                        <p class="mb-0">
                            <i class="bi bi-envelope me-2"></i>
                            {{ $message->sender?->email }}
                        </p>
                    </div>
                </div>
                @if ($message->attachments->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-paperclip"></i>
                                Pièces jointes ({{ $message->attachments->count() }})
                            </h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($message->attachments as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-file-earmark me-2"></i>
                                        {{ $attachment->file_name }}
                                        <small class="text-muted">({{ $attachment->formatted_size }})</small>
                                    </div>
                                    <a href="{{ route('messages.download-attachment', $attachment) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Archive/Désarchive -->
                <div class="card mt-3">
                    <div class="card-body">
                        @if ($message->is_archived)
                            <form action="{{ route('messages.unarchive', $message) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-archive"></i> Désarchiver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('messages.archive', $message) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-archive"></i> Archiver
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Propriété concernée si applicable -->
                @if ($message->property)
                    <div class="card">
                        <img src="{{ $message->property->images->where('is_primary', true)->first()
                            ? Storage::url($message->property->images->where('is_primary', true)->first()->image_path)
                            : 'placeholder.jpg' }}"
                            class="card-img-top" alt="{{ $message->property->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $message->property->title }}</h5>
                            <p class="card-text text-muted">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ $message->property->address }}
                            </p>
                            <a href="{{ route('properties.show', $message->property) }}"
                                class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-building"></i> Voir la propriété
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showReplyForm() {
                document.getElementById('replyForm').classList.remove('d-none');
            }

            function hideReplyForm() {
                document.getElementById('replyForm').classList.add('d-none');
            }

            function confirmDelete() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
                    document.getElementById('deleteForm').submit();
                }
            }
        </script>
    @endpush

    <!-- Formulaire de suppression caché -->
    <form id="deleteForm" action="{{ route('messages.destroy', $message) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endsection
