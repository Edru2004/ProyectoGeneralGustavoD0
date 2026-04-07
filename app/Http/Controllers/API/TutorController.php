<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TutorController extends Controller
{
    // Listar tutores
    public function index()
    {
        $tutores = Tutor::all();
        return response()->json($tutores, 200);
    }

    // Guardar nuevo tutor
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => 'required|string|max:50',
            'apellido_p'  => 'required|string|max:50',
            'no_telefono' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tutor = Tutor::create($request->all());
        return response()->json($tutor, 201);
    }
}