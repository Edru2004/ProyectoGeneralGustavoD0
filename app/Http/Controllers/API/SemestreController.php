<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    public function index()
    {
        return response()->json(Semestre::all(), 200);
    }

    public function store(Request $request)
    {
        $semestre = Semestre::create($request->all());
        return response()->json($semestre, 201);
    }
}