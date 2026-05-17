<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    use HasFactory;

    // DETALLE CLAVE 1: Cambiamos 'semestre' por 'Semestre' (con S mayúscula)
    // para que coincida exactamente con la base de datos de Railway.
    protected $table = 'Semestre';
    protected $primaryKey = 'id_semestre';
    public $timestamps = false; 

    protected $fillable = [
        'nombre_semestre'
    ];

    // DETALLE CLAVE 2: Incluimos el Accessor del proyecto Web.
    /**
     * Lógica automática: Determina el año según el semestre
     */
    public function getAnioEscolarAttribute() 
    {
        if ($this->id_semestre <= 2) return "1er Año";
        if ($this->id_semestre <= 4) return "2do Año";
        return "3er Año";
    }

    // DETALLE CLAVE 3: Súper tip para la API. Esto hace que el campo calculado 'anio_escolar'
    // se incluya automáticamente cada vez que transformes este modelo a JSON.
    protected $appends = ['anio_escolar'];
}