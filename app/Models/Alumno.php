<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'fecha_nacimiento',
        'status', // importante incluirlo ahora que existe
    ];

    // Relación: un alumno puede tener muchas clases
    public function clases()
    {
        return $this->hasMany(\App\Models\Clase::class, 'alumno_id', 'id');
    }
}
