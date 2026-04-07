<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignaciones extends Model
{
    protected $table = 'asignaciones'; // Minúscula
    protected $primaryKey = 'id_asignacion';
    public $timestamps = false;

    protected $fillable = [
        'id_docente', 'id_materia', 'id_grupo', 
        'dia_semana', 'hora_inicio', 'hora_fin', 'aula'
    ];

    public function materia() {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function grupo() {
        return $this->belongsTo(Grupos::class, 'id_grupo', 'id_grupo');
    }
}