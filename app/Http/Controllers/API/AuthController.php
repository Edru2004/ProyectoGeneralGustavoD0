<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Send2FACode;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Intento de inicio de sesión vía API (Paso 1: Validar credenciales y enviar 2FA)
     */
    public function login(Request $request)
    {
        // Validamos los datos de entrada con Validator::make
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 400);
        }

        // Intentamos autenticar con las credenciales
        if (Auth::attempt($request->only('email', 'password'))) {
            
            $user = Auth::user();

            // 1. Generamos el código de 6 dígitos en la BD (Usando tu método unificado)
            $user->generateTwoFactorCode();

            // 2. Enviamos el Gmail con el código de verificación
            try {
                Mail::to($user->email)->send(new Send2FACode($user->two_factor_code));
            } catch (\Exception $e) {
                // Si el servicio de correo falla en Railway, avisamos al cliente
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el correo de verificación.',
                    'error'   => $e->getMessage()
                ], 500);
            }

            // 3. Respuesta de éxito para que el Frontend/App sepa que debe pedir el código
            return response()->json([
                'success' => true,
                'message' => 'Credenciales válidas. Código de verificación enviado al correo.',
                'requires_2fa' => true,
                'user_id' => $user->id // Útil para el siguiente paso de verificación
            ], 200);
        }

        // Si las credenciales fallan
        return response()->json([
            'success' => false,
            'message' => 'Las credenciales no coinciden con nuestros registros.'
        ], 401);
    }

    /**
     * Cierre de sesión en la API (Revocar tokens)
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Si usas Laravel Sanctum, esto elimina el token actual con el que se está navegando
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente y token revocado.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay una sesión activa.'
        ], 401);
    }
}