<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla si no sigue la convención plural
    protected $table = 'clases';

    // Indica que la clave primaria no es un entero autoincremental
    public $incrementing = false;

    // Indica que la clave primaria es de tipo string
    protected $keyType = 'string';
    public $timestamps = false;
    // Lista de columnas que pueden ser asignadas masivamente
    protected $fillable = [
        'id_programa',
        'id_clase', 
        'nombre', 
        'id_maestro', 
        'informacion', 
        'porcentaje', 
        'personal'
    ];

    // Si la clave primaria no es 'id', especifica el nombre de la clave primaria
    protected $primaryKey = 'id_programa';
}

