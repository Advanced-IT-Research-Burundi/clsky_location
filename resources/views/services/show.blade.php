@extends('layouts.admin')

@section('title', 'Détail du service')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Détail du service
                </h5>
                <div>
                    <a href="{{ route('services.edit', $service) }}"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    <a href="{{ route('services.index') }}"
                       class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <strong>Nom :</strong>
                    <p class="mb-0">{{ $service->name }}</p>
                </div>

                <div class="mb-3">
                    <strong>Description :</strong>
                    <p class="mb-0">
                        {{ $service->description ?? 'Aucune description' }}
                    </p>
                </div>

                <div class="mb-3">
                    <strong>Image :</strong><br>
                    @if($service->image)
                        <img src="{{ asset('storage/'.$service->image) }}"
                             alt="Service image"
                             class="img-fluid rounded"
                             style="max-height: 300px;">
                    @else
                        <span class="text-muted">Aucune image</span>
                    @endif
                </div>

                <div class="text-muted">
                    <small>
                        Créé le {{ $service->created_at->format('d/m/Y') }}
                    </small>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
