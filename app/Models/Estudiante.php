<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiante';
    protected $primaryKey = 'id_estudiante';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'apellido_p', 'apellido_m', 'curp', 'email', 
        'sexo', 'fecha_nac', 'no_telefóno', 'colonia', 'calle', 
        'numero', 'id_tutor'
    ];

    // Relación con el Tutor (Para que aparezcan sus datos)
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id_tutor', 'id_tutor');
    }

    // Relación con la Inscripción (Semestre y Grupo)
    public function inscripcion()
    {
        return $this->hasOne(Inscripciones::class, 'id_estudiante', 'id_estudiante');
    }
}