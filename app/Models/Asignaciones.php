<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignaciones extends Model
{
    use HasFactory;

    // Se mantiene en minúscula 'asignaciones' tal como lo tienes definido en ambos lados
    protected $table = 'asignaciones'; 
    protected $primaryKey = 'id_asignacion';
    public $timestamps = false;

    // DETALLE CLAVE 1: Añadimos 'icono_card' y 'color_card' al fillable.
    // Esto permitirá que la API pueda registrar o actualizar el diseño de las tarjetas desde los requests.
    protected $fillable = [
        'id_docente', 
        'id_materia', 
        'id_grupo', 
        'dia_semana', 
        'hora_inicio', 
        'hora_fin', 
        'aula', 
        'icono_card',
        'color_card'
    ];

    // --- RELACIONES ---

    // Relación: Una asignación pertenece a una materia
    public function materia() 
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    // Relación: Una asignación pertenece a un grupo
    public function grupo() 
    {
        return $this->belongsTo(Grupos::class, 'id_grupo', 'id_grupo');
    }

    // DETALLE CLAVE 2: Agregamos la relación con Docente que le faltaba a la API.
    // Súper importante para que cuando la API consulte los horarios, pueda incluir el nombre del maestro.
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }
}