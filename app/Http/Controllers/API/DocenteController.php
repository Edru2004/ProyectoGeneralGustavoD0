<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocenteController extends Controller
{
    public function index()
    {
        // Trae a todos con sus clases, materias y grupos
        $docentes = Docente::with(['asignaciones.materia', 'asignaciones.grupo'])->get();
        
        // TRUCO MAESTRO: Limpiamos la codificación convirtiendo a array y de vuelta
        $data = json_decode(json_encode($docentes), true);

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function show($id)
    {
        $docente = Docente::with(['asignaciones.materia', 'asignaciones.grupo'])->find($id);

        if (!$docente) {
            return response()->json(['mensaje' => 'Docente no encontrado'], 404);
        }

        $data = json_decode(json_encode($docente), true);
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'     => 'required|string|max:50',
            'apellido_p' => 'required|string|max:50',
            'email'      => 'required|email|unique:Docente,email',
            'password'   => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $docente = Docente::create($request->all());
        return response()->json($docente, 201);
    }
}