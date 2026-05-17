<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Calificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CalificacionesController extends Controller
{
    /**
     * Listado general (útil para administradores)
     */
    public function index()
    {
        $calificaciones = Calificaciones::with(['estudiante', 'asignacion.materia'])->get();
        return response()->json($calificaciones, 200);
    }

    /**
     * ADAPTACIÓN DE "misCalificaciones"
     * Obtiene la boleta o lista de calificaciones del estudiante autenticado en la API (ej. desde la app móvil)
     */
    public function misCalificaciones(Request $request)
    {
        // Obtenemos el estudiante autenticado mediante el token de la API
        $user = $request->user();
        
        if (!$user || !isset($user->id_estudiante)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no identificado como estudiante.'
            ], 403);
        }

        // Cargamos las relaciones anidadas tal como lo hacías en Web
        $calificaciones = Calificaciones::with([
            'asignacion.materia', 
            'asignacion.docente'
        ])
        ->where('id_estudiante', $user->id_estudiante)
        ->get();

        return response()->json($calificaciones, 200);
    }

    /**
     * ADAPTACIÓN DE "guardar" (Guardado masivo del profesor)
     * Procesa la lista del GDO enviada desde el cliente API
     */
    public function guardarMasivo(Request $request)
    {
        // Validamos la estructura del lote recibido
        $validator = Validator::make($request->all(), [
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion', // Asegurar nombre exacto en Railway
            'notas'         => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 400);
        }

        $datos = $request->input('notas'); 
        $id_asignacion = $request->input('id_asignacion'); 
        $procesados = [];

        // Iteramos las notas tal como la lógica original del Web
        foreach ($datos as $id_estudiante => $valores) {
            $calificacion = Calificaciones::updateOrCreate(
                [
                    'id_estudiante' => $id_estudiante, 
                    'id_asignacion' => $id_asignacion 
                ],
                [
                    // Mapeamos los sub-campos asegurando un fallback a 0 si no se envían
                    'p1_n1' => $valores['n1'] ?? 0,
                    'p1_n2' => $valores['n2'] ?? 0,
                    'p1_n3' => $valores['n3'] ?? 0,
                ]
            );
            $procesados[] = $calificacion;
        }

        return response()->json([
            'success' => true,
            'message' => '¡Lista del GDO actualizada correctamente!',
            'data'    => $procesados
        ], 200);
    }

    /**
     * ADAPTACIÓN DE "updatePassword"
     * Actualiza la contraseña del usuario logueado desde la API
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = $request->user();

        // Verificar que la contraseña actual sea la correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.'
            ], 422);
        }

        // Encriptar y guardar la nueva contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '¡Contraseña actualizada con éxito!'
        ], 200);
    }
}