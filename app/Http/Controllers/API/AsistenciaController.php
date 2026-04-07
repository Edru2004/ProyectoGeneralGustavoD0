<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsistenciaController extends Controller
{
    /**
     * Muestra la lista de todas las asistencias con sus relaciones.
     */
    public function index()
    {
        // Cargamos las relaciones con estudiante y materia
        $asistencias = Asistencia::with(['estudiante', 'materia'])->get();
        
        // Convertimos a array y forzamos la limpieza de UTF-8 para evitar el error rosa
        $data = json_decode(json_encode($asistencias), true);

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * Registra una nueva asistencia en la base de datos.
     */
    public function store(Request $request)
    {
        // Validamos usando los nombres exactos de tus tablas y columnas
        $validator = Validator::make($request->all(), [
            'id_estudiante' => 'required|exists:Estudiante,id_estudiante',
            'id_materia'    => 'required|exists:materia,id_materia', // 'materia' en minúscula según tu DB
            'estado'        => 'required|in:Presente,Falta,Retardo,Justificada',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Creamos el registro. Si no envías fecha_asistencia u hora_ingreso, 
        // MariaDB usará los valores por defecto (curdate y curtime).
        $asistencia = Asistencia::create($request->all());
        
        return response()->json([
            'mensaje' => 'Asistencia registrada con éxito',
            'data' => $asistencia
        ], 201);
    }

    /**
     * Muestra una asistencia específica por su ID.
     */
    public function show($id)
    {
        $asistencia = Asistencia::with(['estudiante', 'materia'])->find($id);

        if (!$asistencia) {
            return response()->json(['mensaje' => 'Registro de asistencia no encontrado'], 404);
        }

        $data = json_decode(json_encode($asistencia), true);
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
}