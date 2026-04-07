<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asignaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsignacionesController extends Controller
{
   public function index()
{
    // Esto te dará una lista parecida a tu SELECT * de MariaDB pero en JSON
    $asignaciones = Asignaciones::with(['materia', 'grupo'])->get();
    return response()->json($asignaciones);
}
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_docente' => 'required|exists:Docente,id_docente',
            'id_materia' => 'required|exists:Materia,id_materia',
            'id_grupo'   => 'required|exists:Grupos,id_grupo',
            'aula'       => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $asignacion = Asignaciones::create($request->all());
        return response()->json($asignacion, 201);
    }
}