@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
        <div class="col">
            <h3 class="mb-0">Especialidades</h3>
        </div>
        <div class="col text-right">
            <a href="{{ url('specialties/create') }}" class="btn btn-sm btn-success">
                Nueva especialidad
            </a>
        </div>
        </div>
    </div>
    <div class="card-body">
        @if (session('notification'))
        <div class="alert alert-default" role="alert">
            {{ session('notification') }}
        </div>
        @endif
    </div>
    <div class="table-responsive">
        <!-- Projects table -->
        <table class="table align-items-center table-flush">
        <thead class="thead-light">
            <tr>
            <th scope="col">Nombre</th>
            <th scope="col">Descripción</th>
            <th scope="col">Opciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($specialities as $speciality)
            <tr>
            <th scope="row">
                {{ $speciality->name }}
            </th>
            <td>
                {{ str_limit($speciality->description,80) }}
            </td>
            <td>
            <form action="{{ url('/specialties/'.$speciality->id) }}" method="POST">
            @csrf
            @method('DELETE')
                <a href="{{ url('/specialties/'.$speciality->id.'/edit') }}" class="btn btn-sm btn-primary">Editar</a>
                <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>

            </form>
            </td>
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>
</div>
@endsection
