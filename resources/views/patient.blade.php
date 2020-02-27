@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Mis Pacientes</h3>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <!-- Projects table -->
        <table class="table align-items-center table-flush">
        <thead class="thead-light">
            <tr>
            <th scope="col">Nombre</th>
            <th scope="col">E-mail</th>
            <th scope="col">DNI</th>
            <th scope="col">Dirección</th>
            <th scope="col">Total de Citas Atendidas</th>
            </tr>
        </thead>
        <tbody>    
            @foreach ($appointments as $appointment)
                <tr>
                <th scope="row">
                    {{ $appointment->patient->name }}
                </th>
                <td>
                    {{ $appointment->patient->email }}                
                </td>
                <td>
                    {{ $appointment->patient->dni }}                
                </td>
                <td>
                    {{ $appointment->patient->address }}                
                </td>
                <td class="text-center">
                    <h2><span class="badge badge-primary">{{ $appointment->count_pid }}</span></h2>
                    
                </td>
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $appointments->links() }}
    </div>
</div>
@endsection
