<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;

    protected $table = 'clases';
    protected $primaryKey = 'id_clase'; // Clave primaria según tu base
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_programa',
        'alumno_id',
        'nombre',
        'id_maestro',
        'informacion',
        'porcentaje',
        'personal'
    ];

    public $timestamps = false; // Si NO tienes created_at y updated_at en tu tabla

    // Si tienes timestamps, ponlo en true y revisa tu migración
}
