<?php

use Illuminate\Support\Facades\Route;
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

// Esta única línea reemplaza los 5 Route::get/post/put/delete anteriores
// Nota: El parámetro por defecto será {estudiante} para que coincida con el controlador
Route::apiResource('estudiantes', EstudianteController::class);
Route::get('/docentes', [DocenteController::class, 'index']);
Route::post('/docentes', [DocenteController::class, 'store']);
Route::get('/docentes/{id}', [DocenteController::class, 'show']);
Route::get('/tutores', [TutorController::class, 'index']);
Route::post('/tutores', [TutorController::class, 'store']);
Route::get('/semestres', [SemestreController::class, 'index']);
Route::post('/semestres', [SemestreController::class, 'store']);

// Rutas para Grupos
Route::get('/grupos', [GruposController::class, 'index']);
Route::post('/grupos', [GruposController::class, 'store']);
Route::get('/materias', [MateriaController::class, 'index']);
Route::post('/materias', [MateriaController::class, 'store']);
Route::get('/inscripciones', [InscripcionesController::class, 'index']);
Route::post('/inscripciones', [InscripcionesController::class, 'store']);
Route::get('/asignaciones', [AsignacionesController::class, 'index']);
Route::get('/calificaciones', [CalificacionesController::class, 'index']);
Route::get('/asistencias', [AsistenciaController::class, 'index']);