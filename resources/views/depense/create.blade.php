@extends('layouts.admin')

@section('title', 'Ajouter une Dépense')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Ajouter une Dépense</h1>

    <form action="{{ route('depenses.store') }}" method="POST" class="card mb-4" enctype="multipart/form-data">
        @csrf
        @include('depense._form')
    </form>
</div>
@endsection
