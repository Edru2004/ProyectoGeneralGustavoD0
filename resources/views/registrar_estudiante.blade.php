@extends('Index') 

@section('contenido_dinamico')
<div class="container-fluid mt-4">
    <div class="card shadow border-0 p-4">
        <h2 class="fw-bold mb-4">Registrar Nuevo Estudiante</h2>
        
        <form action="{{ route('estudiantes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Nombre(s)</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Apellido Paterno</label>
                    <input type="text" name="apellido_p" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Apellido Materno</label>
                    <input type="text" name="apellido_m" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">CURP</label>
                    <input type="text" name="curp" class="form-control" maxlength="18">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="Mujer">Mujer</option>
                        <option value="Hombre">Hombre</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nac" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Teléfono</label>
                    <input type="text" name="no_telefono" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Calle</label>
                    <input type="text" name="calle" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Número</label>
                    <input type="text" name="numero" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Colonia</label>
                    <input type="text" name="colonia" class="form-control">
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold text-primary">Asignar Tutor Responsable</label>
                    <select name="id_tutor" class="form-select shadow-sm" required>
                        <option value="" selected disabled>Selecciona un tutor...</option>
                        @foreach($tutores as $tutor)
                            <option value="{{ $tutor->id_tutor }}">{{ $tutor->nombre }} - {{ $tutor->parentesco }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('estudiantes.index') }}" class="btn btn-light border">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">Guardar Estudiante</button>
            </div>
        </form>
    </div>
</div>
@endsection