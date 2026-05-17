<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    // DETALLE CLAVE 1: Cambiamos 'tutor' por 'Tutor' (con T mayúscula)
    // para que coincida exactamente con la base de datos de Railway.
    protected $table = 'Tutor';

    protected $primaryKey = 'id_tutor';
    public $timestamps = false;

    // DETALLE CLAVE 2: Actualizamos el fillable con los nuevos campos del Web
    // Agregamos 'curp' y 'municipio' para que la API permita registrarlos o editarlos.
    protected $fillable = [
        'nombre',
        'apellido_p',
        'apellido_m',
        'curp', 
        'parentesco',
        'no_telefono',
        'municipio',
        'ciudad',
        'calle',
        'numero'
    ];

    // --- RELACIONES ---
    
    /**
     * Relación con Estudiantes (Un tutor puede tener muchos estudiantes)
     */
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_tutor', 'id_tutor');
    }
}