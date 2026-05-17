<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asignaciones;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsignacionesController extends Controller
{
    /**
     * Listar todas las asignaciones con sus relaciones (Materia, Grupo y Docente)
     */
    public function index()
    {
        // Añadimos 'docente' a la carga para que el JSON vaya completísimo con el nombre del maestro
        $asignaciones = Asignaciones::with(['materia', 'grupo', 'docente'])->get();
        return response()->json($asignaciones, 200);
    }

    /**
     * EQUIVALENTE AL METHOD "CREATE" DE WEB
     * Devuelve los catálogos necesarios para llenar los formularios/selects en el frontend o app móvil
     */
    public function getFormDependencies()
    {
        return response()->json([
            'docentes' => Docente::all(),
            'materias' => Materia::all(),
            'grupos'   => Grupos::all()
        ], 200);
    }

    /**
     * Guardar una nueva asignación de horario mediante la API
     */
    public function store(Request $request)
    {
        // Unificamos las validaciones de Web y API (con nombres exactos de tablas en Railway)
        $validator = Validator::make($request->all(), [
            'id_docente'   => 'required|exists:docente,id_docente', // Tabla en minúscula
            'id_materia'   => 'required|exists:materia,id_materia', // Tabla en minúscula
            'id_grupo'     => 'required|exists:grupos,id_grupo',   // Tabla en minúscula
            'dia_semana'   => 'required|string|max:20',
            'hora_inicio'  => 'required',
            'hora_fin'     => 'required',
            'aula'         => 'nullable|string|max:20',
            'icono_card'   => 'nullable|string|max:50',
            'color_card'   => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 400);
        }

        // Creamos el registro con todos los datos validados
        $asignacion = Asignaciones::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Horario asignado con éxito',
            'data'    => $asignacion
        ], 201);
    }
}