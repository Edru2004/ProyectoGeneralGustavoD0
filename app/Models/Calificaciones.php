<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificaciones extends Model {
    use HasFactory;
    protected $table = 'calificaciones';
    protected $primaryKey = 'id_calificacion';
    public $timestamps = false;
    protected $fillable = ['id_estudiante', 'id_materia', 'parcial1', 'parcial2', 'parcial3'];

    public function estudiante() { return $this->belongsTo(Estudiante::class, 'id_estudiante'); }
    public function materia() { return $this->belongsTo(Materia::class, 'id_materia'); }
}