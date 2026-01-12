@extends('layouts.admin')

@section('title', 'Profil - CL SKY APARTMENT')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-3">Mon Profil</h1>

            {{-- Message de succès --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Carte --}}
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Informations personnelles -->
                        <h5 class="mb-4">Informations personnelles</h5>

                        <div class="mb-4">
                            <label class="form-label">Nom</label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name', auth()->user()->name) }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email', auth()->user()->email) }}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Sécurité -->
                        <h5 class="mb-4">Sécurité</h5>

                        <div class="mb-4">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Laisser vide pour ne pas changer"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input 
                                type="password" 
                                class="form-control"
                                name="password_confirmation"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            {{-- Suppression du compte --}}
            <div class="card mt-4 border-danger">
                <div class="card-body">
                    <h5 class="text-danger mb-3">Supprimer le compte</h5>
                    <p class="text-muted">
                        Cette action est irréversible.
                    </p>

                    <form action="{{ route('profile.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="btn btn-danger"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')"
                        >
                            <i class="bi bi-trash"></i> Supprimer mon compte
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
