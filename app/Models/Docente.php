<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docente'; 
    protected $primaryKey = 'id_docente';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'apellido_p', 'apellido_m', 'curp',
        'email', 'password', 'telefono', 'estudios',
        'num_cedula_prof', 'rfc'
    ];

    protected $hidden = ['password'];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    public function asignaciones() {
        return $this->hasMany(Asignaciones::class, 'id_docente', 'id_docente');
    }
}