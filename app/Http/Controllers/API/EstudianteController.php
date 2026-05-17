<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller
{
    // Listar estudiantes con TODO: Tutor, Semestre y Grupo
    public function index()
    {
        $estudiantes = Estudiante::with([
            'tutor', 
            'inscripcion.semestre', 
            'inscripcion.grupo'
        ])->get();

        return response()->json($estudiantes);
    }

    // Guardar nuevo alumno e inscribirlo automáticamente
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Creamos al estudiante
            $estudiante = Estudiante::create($request->all());

            // 2. Creamos su inscripción con los IDs que mandes de la App
            Inscripciones::create([
                'id_estudiante' => $estudiante->id_estudiante,
                'id_semestre' => $request->id_semestre,
                'id_grupo' => $request->id_grupo,
                'ciclo_escolar' => '2025-2026',
                'estado_inscripcion' => 'Activo'
            ]);

            return response()->json([
                'mensaje' => 'Estudiante y Tutor registrados correctamente',
                'data' => $estudiante->load(['tutor', 'inscripcion.semestre', 'inscripcion.grupo'])
            ], 201);
        });
    }

    // Ver un estudiante específico con sus detalles
    public function show($id)
    {
        $estudiante = Estudiante::with([
            'tutor', 
            'inscripcion.semestre', 
            'inscripcion.grupo'
        ])->findOrFail($id);

        return response()->json($estudiante);
    }
}