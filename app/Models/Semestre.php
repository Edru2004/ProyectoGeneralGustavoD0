<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    use HasFactory;

    protected $table = 'Semestre';
    protected $primaryKey = 'id_semestre';
    public $timestamps = false; // Como es una tabla de catálogo simple, no solemos usar timestamps

    protected $fillable = ['nombre_semestre'];
}