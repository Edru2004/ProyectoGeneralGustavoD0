<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use App\Models\Docente;
use App\Models\Tutor;
use App\Models\Calificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EstudianteController extends Controller
{
    /**
     * Listar estudiantes con filtros avanzados (Grado, Grupo, Búsqueda por texto)
     */
    public function index(Request $request)
    {
        $query = Estudiante::with(['tutor', 'inscripcion.semestre', 'inscripcion.grupo']);

        // 1. Filtro por Grado (Año)
        if ($request->filled('grado')) {
            $grado = $request->grado;
            $semestres = [($grado * 2) - 1, $grado * 2]; // Grado 1 -> Semestres 1 y 2

            $query->whereHas('inscripcion', function ($q) use ($semestres) {
                $q->whereIn('id_semestre', $semestres);
            });
        }

        // 2. Filtro por Grupo (Ej: A o B)
        if ($request->filled('grupo')) {
            $grupoNombre = $request->grupo;
            $query->whereHas('inscripcion.grupo', function ($q) use ($grupoNombre) {
                $q->where('nombre_grupo', $grupoNombre);
            });
        }

        // 3. Filtro de Búsqueda General
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                    ->orWhere('apellido_p', 'LIKE', "%{$buscar}%")
                    ->orWhere('apellido_m', 'LIKE', "%{$buscar}%")
                    ->orWhere('curp', 'LIKE', "%{$buscar}%")
                    ->orWhere('email', 'LIKE', "%{$buscar}%");
            });
        }

        // Retornamos la colección de datos filtrada (puedes usar ->get() o ->paginate() si la app maneja scroll infinito)
        $estudiantes = $query->orderBy('apellido_p', 'asc')->get();

        return response()->json([
            'success' => true,
            'count'   => $estudiantes->count(),
            'data'    => $estudiantes
        ], 200);
    }

    /**
     * Ver expediente de un estudiante específico
     */
    public function show($id)
    {
        $estudiante = Estudiante::with([
            'tutor', 
            'inscripcion.semestre', 
            'inscripcion.grupo'
        ])->find($id);

        if (!$estudiante) {
            return response()->json(['success' => false, 'message' => 'Estudiante no encontrado'], 404);
        }

        return response()->json($estudiante, 200);
    }

    /**
     * Guardar nuevo alumno e inscribirlo automáticamente dentro de una transacción segura
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => 'required|string|max:50',
            'curp'        => 'required|unique:estudiante,curp',
            'email'       => 'required|email|unique:estudiante,email',
            'password'    => 'required|min:6',
            'id_semestre' => 'required|exists:semestre,id_semestre',
            'id_grupo'    => 'required|exists:grupos,id_grupo'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        return DB::transaction(function () use ($request) {
            $data = $request->all();
            $data['password'] = bcrypt($request->password);

            // 1. Creamos al estudiante
            $estudiante = Estudiante::create($data);

            // 2. Creamos su inscripción correspondiente
            Inscripciones::create([
                'id_estudiante'      => $estudiante->id_estudiante,
                'id_semestre'        => $request->id_semestre,
                'id_grupo'           => $request->id_grupo,
                'ciclo_escolar'      => '2025-2026',
                'estado_inscripcion' => 'Activo'
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Estudiante registrado e inscrito correctamente',
                'data'    => $estudiante->load(['tutor', 'inscripcion.semestre', 'inscripcion.grupo'])
            ], 201);
        });
    }

    /**
     * Actualizar los datos de un estudiante e inscripción asociada
     */
    public function update(Request $request, $id)
    {
        $estudiante = Estudiante::find($id);
        if (!$estudiante) {
            return response()->json(['success' => false, 'message' => 'Estudiante no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'curp'        => 'sometimes|unique:estudiante,curp,' . $id . ',id_estudiante',
            'email'       => 'sometimes|email|unique:estudiante,email,' . $id . ',id_estudiante',
            'password'    => 'nullable|min:6',
            'id_semestre' => 'sometimes|exists:semestre,id_semestre',
            'id_grupo'    => 'sometimes|exists:grupos,id_grupo'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $estudiante->update($data);

        // Actualizamos la inscripción si los parámetros de la escuela vienen en la petición
        if ($estudiante->inscripcion && $request->has('id_semestre') && $request->has('id_grupo')) {
            $estudiante->inscripcion->update([
                'id_semestre' => $request->id_semestre,
                'id_grupo'    => $request->id_grupo
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos del estudiante actualizados con éxito.',
            'data'    => $estudiante->load(['inscripcion.semestre', 'inscripcion.grupo'])
        ], 200);
    }

    /**
     * Eliminar un estudiante
     */
    public function destroy($id)
    {
        $estudiante = Estudiante::find($id);
        if (!$estudiante) {
            return response()->json(['success' => false, 'message' => 'Estudiante no encontrado'], 404);
        }

        $estudiante->delete();
        return response()->json(['success' => true, 'message' => 'Estudiante eliminado de la base de datos.'], 200);
    }

    /**
     * MÉTODO DE CONTEOS DE DASHBOARD (Para Administrador)
     */
    public function inicioAdmin()
    {
        return response()->json([
            'success'          => true,
            'totalEstudiantes' => Estudiante::count(),
            'totalDocentes'    => Docente::count(),
            'totalTutores'     => Tutor::count(),
        ], 200);
    }

    /**
     * DASHBOARD EXCLUSIVO DEL ESTUDIANTE LOGUEADO
     */
    public function dashboardEstudiante(Request $request)
    {
        $estudiante = $request->user(); // Identificamos mediante token
        $estudiante->load(['inscripcion.semestre', 'inscripcion.grupo', 'tutor']);

        return response()->json([
            'success' => true,
            'perfil'  => $estudiante
        ], 200);
    }

    /**
     * CONSULTAR CALIFICACIONES (Boleta digital)
     */
    public function verCalificaciones(Request $request)
    {
        $estudiante = $request->user();

        $calificaciones = Calificaciones::with(['asignacion.materia', 'asignacion.docente'])
            ->where('id_estudiante', $estudiante->id_estudiante)
            ->get();

        return response()->json([
            'success'        => true,
            'calificaciones' => $calificaciones
        ], 200);
    }

    /**
     * ACTUALIZAR FOTO DE PERFIL DEL ALUMNO
     */
    public function updateFotoE(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $estudiante = $request->user();

        if ($request->hasFile('foto')) {
            if ($estudiante->foto && $estudiante->foto != 'default-student.png') {
                $ruta = public_path('img/estudiantes/' . $estudiante->foto);
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }

            $nombreFoto = 'est_' . time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('img/estudiantes'), $nombreFoto);

            $estudiante->foto = $nombreFoto;
            $estudiante->save();
        }

        return response()->json(['success' => true, 'message' => '¡Foto de perfil estudiantil actualizada!', 'foto' => $estudiante->foto], 200);
    }

    /**
     * ACTUALIZAR CONTRASEÑA DEL ALUMNO
     */
    public function updatePasswordE(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $estudiante = $request->user();

        if (!Hash::check($request->current_password, $estudiante->password)) {
            return response()->json(['success' => false, 'message' => 'La contraseña actual es incorrecta.'], 422);
        }

        $estudiante->password = Hash::make($request->new_password);
        $estudiante->save();

        return response()->json(['success' => true, 'message' => '¡Contraseña estudiantil cambiada con éxito!'], 200);
    }

    /**
     * DESCARGAS DE REPORTES PDF (Disponibles si se renderiza en backend)
     */
    public function descargarPDF($id)
    {
        $estudiante = Estudiante::with(['tutor', 'inscripcion.semestre', 'calificaciones.materia'])->find($id);
        if (!$estudiante) return response()->json(['message' => 'No encontrado'], 404);

        $pdf = Pdf::loadView('estudiantes.pdf', compact('estudiante'));
        return $pdf->download('Reporte_' . $estudiante->curp . '.pdf');
    }

    public function reporteGeneral()
    {
        $estudiantes = Estudiante::with(['tutor', 'inscripcion.semestre', 'inscripcion.grupo'])->get();
        $pdf = Pdf::loadView('estudiantes.pdf_general', compact('estudiantes'))->setPaper('letter', 'landscape');
        return $pdf->download('Reporte_General_GDO.pdf');
    }

    public function descargarBoletaPDF(Request $request)
    {
        $estudiante = $request->user();
        $calificaciones = Calificaciones::with(['asignacion.materia', 'asignacion.docente'])
            ->where('id_estudiante', $estudiante->id_estudiante)
            ->get();

        $pdf = Pdf::loadView('estudiantes.boleta_pdf', compact('estudiante', 'calificaciones'));
        return $pdf->download('Boleta_' . $estudiante->nombre . '.pdf');
    }
}