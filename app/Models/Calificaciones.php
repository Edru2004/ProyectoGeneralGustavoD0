<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificaciones extends Model 
{
    use HasFactory;

    protected $table = 'calificaciones'; 
    protected $primaryKey = 'id_calificacion';
    public $timestamps = false; 

    // DETALLE CLAVE 1: Actualizamos el fillable con las nuevas columnas de la BD en Railway
    // Quitamos 'parcial1', 'parcial2', etc., y añadimos el id_asignacion junto con las sub-notas
    protected $fillable = [
        'id_estudiante',
        'id_asignacion', 
        'id_materia',    // Obligatoria
        'p1_n1', 'p1_n2', 'p1_n3', // Parcial 1 (ej: Tareas, Examen, Proyecto)
        'p2_n1', 'p2_n2', 'p2_n3', // Parcial 2
        'p3_n1', 'p3_n2', 'p3_n3', // Parcial 3
    ];

    // --- RELACIONES ---

    // Relación vital para conectar con los horarios y docentes
    public function asignacion()
    {
        return $this->belongsTo(Asignaciones::class, 'id_asignacion', 'id_asignacion');
    }

    // Corregimos la llave foránea explícita para que la API no se confunda
    public function estudiante() 
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    // DETALLE CLAVE 2: Copiamos la relación avanzada hasOneThrough.
    // Esto te permitirá hacer en tus controladores de la API cosas como: $calificacion->materia
    // trayendo la información de la materia de forma transparente a través de las asignaciones.
    public function materia()
    {
        return $this->hasOneThrough(
            Materia::class, 
            Asignaciones::class, 
            'id_asignacion', // Llave foránea en Asignaciones
            'id_materia',    // Llave foránea en Materia
            'id_asignacion', // Llave local en Calificaciones
            'id_materia'     // Llave local en Asignaciones
        );
    }
}