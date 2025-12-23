@extends('layouts.admin')

@section('title', 'Modifier la Dépense')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Modifier la Dépense</h1>

    <form action="{{ route('depenses.update', $depense->id) }}" method="POST" class="card mb-4" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('depense._form')
    </form>
</div>
@endsection
