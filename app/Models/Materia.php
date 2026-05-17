<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    // Se mantiene en minúscula 'materia' tal como está en el Web
    protected $table = 'materia'; 
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = [
        'nombre_materia', 
        'no_horas', 
        'creditos', 
        'id_semestre'
    ];

    // --- RELACIONES ---

    // Conservamos esta relación de la API. Es excelente para que cuando consultes 
    // una materia, puedas saber de inmediato a qué semestre pertenece en tus respuestas JSON.
    /**
     * Una materia pertenece a un semestre
     */
    public function semestre() 
    {
        return $this->belongsTo(Semestre::class, 'id_semestre', 'id_semestre');
    }
}