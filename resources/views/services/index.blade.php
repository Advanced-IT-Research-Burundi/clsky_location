@extends('layouts.admin')

@section('title', 'Services')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-tools"></i> Gestion des services
    </h4>

    <a href="{{ route('services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouveau service
    </a>
</div>

<div class="card">
    <div class="card-body">

        @if($services->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    @if($service->image)
                                        <img src="{{ asset('storage/'.$service->image) }}"
                                             alt="service"
                                             class="rounded"
                                             width="50"
                                             height="50"
                                             style="object-fit: cover;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <strong>{{ $service->name }}</strong>
                                </td>

                                <td>
                                    {{ Str::limit($service->description, 50) }}
                                </td>

                                <td>
                                    {{ $service->created_at->format('d/m/Y') }}
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('services.show', $service) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('services.edit', $service) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('services.destroy', $service) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer ce service ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-info-circle fs-1"></i>
                <p class="mt-3">Aucun service enregistré</p>
                <a href="{{ route('services.create') }}" class="btn btn-primary mt-2">
                    Ajouter un service
                </a>
            </div>
        @endif

    </div>
</div>

@endsection
