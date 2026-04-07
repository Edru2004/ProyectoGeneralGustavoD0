@extends('Index')

@section('contenido_dinamico')
<div class="d-flex justify-content-between align-items-center mb-4 px-3">
    <h2 style="font-weight: 700; color: #333;">Gestión de Estudiantes</h2>
    <a href="{{ route('estudiantes.create') }}" class="btn btn-primary rounded-pill shadow-sm">
        <i class="bi bi-plus-lg"></i> NUEVO REGISTRO
    </a>
</div>

<div class="table-container"> <table class="table-custom"> <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>CURP / Email</th>
                <th>Tutor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiantes as $est)
            <tr>
                <td>{{ $est->id_estudiante }}</td>
                <td><strong>{{ $est->nombre }} {{ $est->apellido_p }}</strong></td>
                <td>{{ $est->email }}</td>
                <td>{{ $est->tutor->nombre ?? 'N/A' }}</td>
                <td>
                    <button class="btn-op"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn-op text-danger"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="table-footer"> Total de estudiantes registrados: {{ count($estudiantes) }}
    </div>
</div>
@endsection