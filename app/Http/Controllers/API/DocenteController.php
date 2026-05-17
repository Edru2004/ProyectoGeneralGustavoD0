<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Asignaciones;
use App\Models\Estudiante;
use App\Models\Calificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class DocenteController extends Controller
{
    /**
     * Listar todos los docentes con sus asignaciones estructuradas.
     */
    public function index()
    {
        $docentes = Docente::with(['asignaciones.materia', 'asignaciones.grupo'])->get();
        
        // Mantenemos tu truco maestro de limpieza de codificación unicode
        $data = json_decode(json_encode($docentes), true);
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * Ver el expediente detallado de un docente específico.
     */
    public function show($id)
    {
        $docente = Docente::with(['asignaciones.materia', 'asignaciones.grupo'])->find($id);

        if (!$docente) {
            return response()->json(['success' => false, 'message' => 'Docente no encontrado'], 404);
        }

        $data = json_decode(json_encode($docente), true);
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * Crear un nuevo docente desde la API.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'     => 'required|string|max:50',
            'apellido_p' => 'required|string|max:50',
            'email'      => 'required|email|unique:docente,email', // En minúscula según tu estándar DB
            'password'   => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $data = $request->all();
        $data['password'] = bcrypt($request->password);

        $docente = Docente::create($data);
        return response()->json(['success' => true, 'data' => $docente], 201);
    }

    /**
     * Actualizar los datos del docente.
     */
    public function update(Request $request, $id)
    {
        $docente = Docente::find($id);
        if (!$docente) {
            return response()->json(['success' => false, 'message' => 'Docente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre'     => 'sometimes|string|max:50',
            'apellido_p' => 'sometimes|string|max:50',
            'email'      => 'sometimes|email|unique:docente,email,' . $id . ',id_docente',
            'password'   => 'nullable|min:6',
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

        $docente->update($data);
        return response()->json(['success' => true, 'message' => 'Docente actualizado', 'data' => $docente], 200);
    }

    /**
     * Eliminar en cascada un docente y sus registros dependientes.
     */
    public function destroy($id)
    {
        $docente = Docente::find($id);
        if (!$docente) {
            return response()->json(['success' => false, 'message' => 'Docente no encontrado'], 404);
        }

        // 1. Buscamos todas las asignaciones del docente
        $asignacionesIds = $docente->asignaciones()->pluck('id_asignacion');

        // 2. Borramos las calificaciones vinculadas a esas asignaciones
        \DB::table('calificaciones')->whereIn('id_asignacion', $asignacionesIds)->delete();

        // 3. Borramos las asignaciones del docente
        $docente->asignaciones()->delete();

        // 4. Finalmente, borramos al docente
        $docente->delete();

        return response()->json(['success' => true, 'message' => 'Docente y registros vinculados eliminados correctamente.'], 200);
    }

    /**
     * Dashboard del maestro autenticado: Retorna sus clases asignadas.
     */
    public function dashboard(Request $request)
    {
        $docente = $request->user(); // Obtenemos el usuario autenticado vía token
        
        $misClases = Asignaciones::with(['materia', 'grupo']) 
            ->where('id_docente', $docente->id_docente)
            ->get();

        return response()->json(['success' => true, 'clases' => $misClases], 200);
    }

    /**
     * Cargar la lista de alumnos inscritos en una asignación con sus respectivas notas.
     */
    public function verLista($id_asignacion) 
    {
        $asignacion = Asignaciones::with(['materia', 'grupo'])->find($id_asignacion);

        if (!$asignacion) {
            return response()->json(['success' => false, 'message' => 'Asignación no encontrada'], 404);
        }

        $alumnos = Estudiante::whereHas('inscripcion', function($query) use ($asignacion) {
            $query->where('id_grupo', $asignacion->id_grupo);
        })
        ->with(['calificaciones' => function($query) use ($asignacion) {
            $query->where('id_materia', $asignacion->id_materia);
        }])
        ->orderBy('apellido_p')
        ->get();

        return response()->json([
            'success' => true,
            'asignacion' => $asignacion,
            'alumnos' => $alumnos
        ], 200);
    }

    /**
     * Guardar/actualizar calificaciones de un grupo desde la API.
     */
    public function guardarCalificaciones(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'notas'         => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $datos = $request->input('notas'); 
        $id_asignacion = $request->input('id_asignacion');
        $asignacion = Asignaciones::findOrFail($id_asignacion);

        foreach ($datos as $id_estudiante => $valores) {
            Calificaciones::updateOrCreate(
                [
                    'id_estudiante' => $id_estudiante, 
                    'id_asignacion' => $id_asignacion,
                    'id_materia'    => $asignacion->id_materia 
                ],
                [
                    'p1_n1' => floatval($valores['n1'] ?? 0),
                    'p1_n2' => floatval($valores['n2'] ?? 0),
                    'p1_n3' => floatval($valores['n3'] ?? 0),
                ]
            );
        }

        return response()->json(['success' => true, 'message' => '¡Lista de calificaciones del GDO actualizada!'], 200);
    }

    /**
     * Descargar el PDF del Horario del maestro.
     */
    public function descargarHorario($id)
    {
        $docente = Docente::find($id);
        if (!$docente) {
            return response()->json(['success' => false, 'message' => 'Docente no encontrado'], 404);
        }

        $asignaciones = Asignaciones::where('id_docente', $id)
                            ->with(['materia', 'grupo.semestre'])
                            ->get();

        // Genera el archivo en la nube. Nota: Tu vista de PDF debe estar disponible en la API si renderizas HTML a PDF aquí
        $pdf = Pdf::loadView('docentes.pdf_horario', compact('docente', 'asignaciones'));

        return $pdf->download('Horario_'.$docente->apellido_p.'.pdf');
    }

    /**
     * Cambiar el diseño visual de la tarjeta de la clase desde la API.
     */
    public function actualizarEstilo(Request $request, $id)
    {
        $clase = Asignaciones::find($id);
        if (!$clase) {
            return response()->json(['success' => false, 'message' => 'Clase no encontrada'], 404);
        }

        $clase->update([
            'color_card' => $request->color_card,
            'icono_card' => $request->icono_card
        ]);

        return response()->json(['success' => true, 'message' => '¡Estilo de la clase actualizado correctamente!', 'data' => $clase], 200);
    }

    /**
     * Subir foto de perfil del docente autenticado vía API.
     */
    public function updateFotoD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $docente = $request->user();

        if ($request->hasFile('foto')) {
            if ($docente->foto && $docente->foto != 'default-docente.png') {
                $ruta = public_path('img/docentes/' . $docente->foto);
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }

            $nombreFoto = 'doc_' . time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('img/docentes'), $nombreFoto);

            $docente->foto = $nombreFoto;
            $docente->save();
        }

        return response()->json(['success' => true, 'message' => '¡Foto de perfil actualizada!', 'foto' => $docente->foto], 200);
    }

    /**
     * Cambiar contraseña del docente autenticado vía API.
     */
    public function updatePasswordD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $docente = $request->user();

        if (!Hash::check($request->current_password, $docente->password)) {
            return response()->json(['success' => false, 'message' => 'La contraseña actual no coincide.'], 422);
        }

        $docente->password = Hash::make($request->new_password);
        $docente->save();

        return response()->json(['success' => true, 'message' => '¡Contraseña actualizada correctamente!'], 200);
    }
}