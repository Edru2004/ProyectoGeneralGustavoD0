<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Calificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalificacionesController extends Controller
{
    public function index()
    {
        $calificaciones = Calificaciones::with(['estudiante', 'materia'])->get();
        return response()->json($calificaciones, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_estudiante' => 'required|exists:Estudiante,id_estudiante',
            'id_materia'    => 'required|exists:Materia,id_materia',
            'parcial1'      => 'numeric|between:0,10',
            'parcial2'      => 'numeric|between:0,10',
            'parcial3'      => 'numeric|between:0,10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $calificacion = Calificaciones::create($request->all());
        return response()->json([
            'mensaje' => 'Calificación guardada',
            'data' => $calificacion
        ], 201);
    }
}