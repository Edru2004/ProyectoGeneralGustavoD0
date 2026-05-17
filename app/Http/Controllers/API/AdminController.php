<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Listar todos los usuarios en formato JSON.
     */
    public function index()
    {
        $usuarios = User::all();
        return response()->json($usuarios, 200);
    }

    /**
     * Registrar un nuevo administrador mediante la API.
     */
    public function store(Request $request)
    {
        // Usamos Validator::make tal como en tu ejemplo de Asignaciones
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // Si la validación falla, retornamos los errores con estado 400 (Bad Request)
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 400);
        }

        // Creamos el usuario administrador
        $usuario = User::create([
            'name'     => $request->nombre,
            'email'    => $request->email,
            // Nota: Si mantienes el "hashed" cast en tu modelo User, 
            // Laravel ya lo encripta solo, pero dejar el Hash::make previene fallos.
            'password' => Hash::make($request->password), 
            'rol'      => 'admin', 
        ]);

        // Retornamos el usuario creado con un estado 201 (Created)
        return response()->json([
            'success' => true,
            'message' => '¡Administrador creado con éxito!',
            'data'    => $usuario
        ], 201);
    }

    /**
     * Promover un usuario existente a administrador vía API.
     */
    public function promoverUsuario($id)
    {
        // Buscamos al usuario. Si no existe, findOrFail lanzará un error 404 automáticamente.
        $user = User::findOrFail($id);
        
        $user->rol = 'admin';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '¡Permisos actualizados! El usuario ahora es administrador.',
            'data'    => $user
        ], 200);
    }
}