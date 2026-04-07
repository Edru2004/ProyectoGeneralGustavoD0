<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    // Nombre de la tabla en MySQL
    protected $table = 'Tutor';

    // Llave primaria
    protected $primaryKey = 'id_tutor';

    // Campos que se pueden llenar
    protected $fillable = [
        'nombre',
        'apellido_p',
        'apellido_m',
        'parentesco',
        'no_telefono',
        'ciudad',
        'calle',
        'numero'
    ];

    // Relación con Estudiantes (Un tutor puede tener muchos estudiantes)
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_tutor', 'id_tutor');
    }
}