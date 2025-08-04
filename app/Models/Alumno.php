<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $primaryKey = 'id_alumno';
    public $incrementing = true;
    protected $keyType = 'int';


    protected $fillable = [
        'nombre',
        'fecha_nac',
        'celular',
        'tutor',
        'tutor_2',
        'telefono',
        'telefono_2',
        'hist_medico',
        'status',
        'beca'
    ];
}

