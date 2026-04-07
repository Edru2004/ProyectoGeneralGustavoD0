<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MateriaController extends Controller
{
    // Listar materias con su semestre
    public function index()
    {
        $materias = Materia::with('semestre')->get();
        return response()->json($materias, 200);
    }

    // Guardar nueva materia
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_materia' => 'required|string|max:100',
            'id_semestre'    => 'required|exists:Semestre,id_semestre',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $materia = Materia::create($request->all());
        return response()->json($materia, 201);
    }
}