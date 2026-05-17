<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EstudianteController;
use App\Http\Controllers\API\DocenteController;
use App\Http\Controllers\API\TutorController;
use App\Http\Controllers\API\SemestreController;
use App\Http\Controllers\API\GruposController;
use App\Http\Controllers\API\MateriaController;
use App\Http\Controllers\API\InscripcionesController;
use App\Http\Controllers\API\AsignacionesController;
use App\Http\Controllers\API\CalificacionesController;
use App\Http\Controllers\API\AsistenciaController;

/*
|--------------------------------------------------------------------------
| API Routes - Plataforma Digital GDO
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTAS PÚBLICAS / AUTENTICACIÓN
// ==========================================
Route::post('/login', [AuthController::class, 'login']);
// Aquí puedes agregar tu ruta para verificar el token de 2FA una vez que la crees:
// Route::post('/verify-2fa', [AuthController::class, 'verifyOtp']);


// ==========================================
// 2. RUTAS PROTEGIDAS (Requieren Token Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Cierre de sesión ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Módulo: Administrador / Recursos Globales (API Resources) ---
    Route::apiResource('estudiantes', EstudianteController::class);
    Route::apiResource('docentes', DocenteController::class);
    
    Route::apiResource('tutores', TutorController::class)->only(['index', 'store']);
    Route::apiResource('semestres', SemestreController::class)->only(['index', 'store']);
    Route::apiResource('grupos', GruposController::class)->only(['index', 'store']);
    Route::apiResource('materias', MateriaController::class)->only(['index', 'store']);
    Route::apiResource('inscripciones', InscripcionesController::class)->only(['index', 'store']);
    
    Route::get('/asignaciones', [AsignacionesController::class, 'index']);
    Route::get('/calificaciones', [CalificacionesController::class, 'index']);
    Route::get('/asistencias', [AsistenciaController::class, 'index']);
    
    // Estadísticas globales para el Dashboard del Administrador
    Route::get('/admin/conteos-inicio', [EstudianteController::class, 'inicioAdmin']);


    // ==========================================
    // 3. ESPACIO EXCLUSIVO DEL DOCENTE
    // ==========================================
    Route::prefix('docente')->group(function () {
        // Dashboard del maestro (Ver sus clases asignadas)
        Route::get('/dashboard', [DocenteController::class, 'dashboard']);
        
        // Cargar lista de alumnos de una materia/grupo para calificar
        Route::get('/clases/{id_asignacion}/lista', [DocenteController::class, 'verLista']);
        
        // Guardar/Actualizar calificaciones masivas (Lista del GDO)
        Route::post('/clases/guardar-notas', [DocenteController::class, 'guardarCalificaciones']);
        
        // Personalización visual de sus tarjetas de clase
        Route::put('/clases/{id}/estilo', [DocenteController::class, 'actualizarEstilo']);
        
        // Gestión de Perfil del Maestro
        Route::post('/perfil/foto', [DocenteController::class, 'updateFotoD']);
        Route::post('/perfil/password', [DocenteController::class, 'updatePasswordD']);
    });


    // ==========================================
    // 4. ESPACIO EXCLUSIVO DEL ESTUDIANTE
    // ==========================================
    Route::prefix('alumno')->group(function () {
        // Perfil y datos escolares del alumno logueado
        Route::get('/dashboard', [EstudianteController::class, 'dashboardEstudiante']);
        
        // Consultar su boleta digital de calificaciones
        Route::get('/boleta', [EstudianteController::class, 'verCalificaciones']);
        
        // Gestión de Perfil del Alumno
        Route::post('/perfil/foto', [EstudianteController::class, 'updateFotoE']);
        Route::post('/perfil/password', [EstudianteController::class, 'updatePasswordE']);
    });


    // ==========================================
    // 5. GENERACIÓN Y DESCARGA DE REPORTES (PDFs)
    // ==========================================
    Route::get('/reportes/estudiante/{id}', [EstudianteController::class, 'descargarPDF']);
    Route::get('/reportes/general-estudiantes', [EstudianteController::class, 'reporteGeneral']);
    Route::get('/reportes/docente/{id}/horario', [DocenteController::class, 'descargarHorario']);
    Route::get('/reportes/alumno/mi-boleta-pdf', [EstudianteController::class, 'descargarBoletaPDF']);

});