<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// DETALLE CLAVE 1: Cambiamos Model por Authenticatable para que la API 
// pueda autenticar a los estudiantes (por ejemplo, usando Sanctum o tokens)
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Estudiante extends Authenticatable 
{
    use HasFactory, Notifiable;

    // DETALLE CLAVE 2: En la API tenías 'estudiante' con minúscula. 
    // Lo cambiamos a 'Estudiante' para que coincida exactamente con tu nueva BD en Railway.
    protected $table = 'Estudiante';
    protected $primaryKey = 'id_estudiante';
    public $timestamps = false;

    // DETALLE CLAVE 3: Actualizamos todas las columnas nuevas de tu BD
    // (Cambiamos 'no_telefóno' por 'telefono', añadimos 'municipio', 'localidad', la contraseña, fotos y 2FA)
    protected $fillable = [
        'nombre',
        'apellido_p',
        'apellido_m',
        'curp',
        'email',
        'password',
        'sexo',
        'fecha_nac',
        'telefono',
        'municipio',
        'localidad',
        'calle',
        'numero',
        'id_tutor',
        'two_factor_code',
        'two_factor_expires_at',
        'foto',
    ];

    // DETALLE CLAVE 4: Súper importante para la API. Oculta el password y token
    // para que jamás se envíen en las respuestas JSON hacia el frontend o la app móvil.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // --- RELACIONES ---
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id_tutor', 'id_tutor');
    }

    public function inscripcion()
    {
        return $this->hasOne(Inscripciones::class, 'id_estudiante', 'id_estudiante');
    }

    // DETALLE CLAVE 5: Agregamos la relación de calificaciones que le faltaba a la API
    public function calificaciones()
    {
        return $this->hasMany(\App\Models\Calificaciones::class, 'id_estudiante', 'id_estudiante');
    }

    // --- FUNCIONES DE DOBLE FACTOR (2FA) ---
    // Si tu API va a manejar el login con código, estas funciones te van a servir aquí también
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