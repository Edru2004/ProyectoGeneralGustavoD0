<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// DETALLE CLAVE 1: Cambiamos Model por Authenticatable para que la API 
// pueda autenticar e iniciar sesión con los Docentes (ej. usando Sanctum)
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Docente extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $timestamps = false;

    // DETALLE CLAVE 2: Actualizamos el fillable con toda la estructura nueva
    // Agregamos la dirección detallada, los campos de 2FA y la foto
    protected $fillable = [
        'nombre',
        'apellido_p',
        'apellido_m',
        'curp',
        'email',
        'password',
        'telefono',
        'municipio',
        'localidad',
        'calle',
        'numero',
        'estudios',
        'num_cedula_prof',
        'rfc',
        'two_factor_code',
        'two_factor_expires_at',
        'foto',
    ];

    /**
     * Ocultar el password para que no salga en consultas JSON
     */
    protected $hidden = [
        'password',
        'remember_token', // Añadido por seguridad al usar Authenticatable
    ];

    /**
     * Mutator para encriptar la contraseña automáticamente en la API
     */
    public function setPasswordAttribute($value) 
    {
        // Solo encripta si el valor no viene ya encriptado
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Relación con las asignaciones de clases
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignaciones::class, 'id_docente', 'id_docente');
    }

    // --- FUNCIONES DE DOBLE FACTOR (2FA) ---
    // Copiadas del Web por si tu API maneja la verificación de códigos
    public function generateTwoFactorCode()
    {
        $this->timestamps = false; 
        $this->two_factor_code = rand(100000, 999999);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->save();
    }
}