@extends('Index') 

@section('contenido_dinamico')
<div class="container-fluid mt-4">
    <div class="card shadow border-0 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Editar Información del Estudiante</h2>
            <span class="badge bg-primary px-3 py-2">ID: {{ $estudiante->id_estudiante }}</span>
        </div>
        
        <form action="{{ route('estudiantes.update', $estudiante->id_estudiante) }}" method="POST">
            @csrf
            @method('PUT') <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Nombre(s)</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $estudiante->nombre }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Apellido Paterno</label>
                    <input type="text" name="apellido_p" class="form-control" value="{{ $estudiante->apellido_p }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Apellido Materno</label>
                    <input type="text" name="apellido_m" class="form-control" value="{{ $estudiante->apellido_m }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">CURP</label>
                    <input type="text" name="curp" class="form-control" value="{{ $estudiante->curp }}" maxlength="18">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email Institucional</label>
                    <input type="email" name="email" class="form-control" value="{{ $estudiante->email }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="Mujer" {{ $estudiante->sexo == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                        <option value="Hombre" {{ $estudiante->sexo == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="Otro" {{ $estudiante->sexo == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nac" class="form-control" value="{{ $estudiante->fecha_nac }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Teléfono de Contacto</label>
                    <input type="text" name="no_telefono" class="form-control" value="{{ $estudiante->no_telefono }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Calle</label>
                    <input type="text" name="calle" class="form-control" value="{{ $estudiante->calle }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Número</label>
                    <input type="text" name="numero" class="form-control" value="{{ $estudiante->numero }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Colonia</label>
                    <input type="text" name="colonia" class="form-control" value="{{ $estudiante->colonia }}">
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold text-primary">Tutor Responsable Actual</label>
                    <select name="id_tutor" class="form-select shadow-sm" required>
                        @foreach($tutores as $tutor)
                            <option value="{{ $tutor->id_tutor }}" {{ $estudiante->id_tutor == $tutor->id_tutor ? 'selected' : '' }}>
                                {{ $tutor->nombre }} - ({{ $tutor->parentesco }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('estudiantes.index') }}" class="btn btn-light border">Cancelar</a>
                <button type="submit" class="btn btn-success px-5 shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Actualizar Datos
                </button>
            </div>
        </form>
    </div>
</div>
@endsection