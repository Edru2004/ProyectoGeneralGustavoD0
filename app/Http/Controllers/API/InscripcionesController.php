<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inscripciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InscripcionesController extends Controller
{
    // Listar todas las inscripciones con sus detalles
    public function index()
    {
        $inscripciones = Inscripciones::with(['estudiante', 'semestre', 'grupo'])->get();
        return response()->json($inscripciones, 200);
    }

    // Registrar una nueva inscripción
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_estudiante' => 'required|exists:Estudiante,id_estudiante',
            'id_semestre'   => 'required|exists:Semestre,id_semestre',
            'id_grupo'      => 'required|exists:Grupos,id_grupo',
            'ciclo_escolar' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Datos inválidos',
                'mensajes' => $validator->errors()
            ], 400);
        }

        $inscripcion = Inscripciones::create($request->all());

        return response()->json([
            'mensaje' => 'Inscripción realizada con éxito',
            'data' => $inscripcion
        ], 201);
    }
}