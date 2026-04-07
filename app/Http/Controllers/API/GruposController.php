<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grupos;
use Illuminate\Http\Request;

class GruposController extends Controller
{
    public function index()
    {
        return response()->json(Grupos::all());
    }

    public function store(Request $request)
    {
        $grupo = Grupos::create($request->all());
        return response()->json($grupo, 201);
    }
}