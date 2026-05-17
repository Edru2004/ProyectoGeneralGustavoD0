<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripciones extends Model
{
    // DETALLE CLAVE 1: Cambiamos 'inscripciones' por 'Inscripciones' (con I mayúscula)
    // para que coincida exactamente con tu base de datos actualizada en Railway.
    protected $table = 'Inscripciones';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_estudiante', 
        'id_semestre', 
        'id_grupo', 
        'ciclo_escolar', 
        'estado_inscripcion'
    ];

    // --- RELACIONES ---

    // DETALLE CLAVE 2: Conservamos esta relación. Es fundamental para que la API
    // pueda saber a qué alumno le pertenece un registro de inscripción.
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class, 'id_semestre', 'id_semestre');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupos::class, 'id_grupo', 'id_grupo');
    }
}