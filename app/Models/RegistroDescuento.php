<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroDescuento extends Model
{
    protected $table = 'registro_descuentos';
    protected $primaryKey = 'id_descuento';
    public $timestamps = true;

    protected $fillable = [
        'id_alumno','id_programa','periodo',
        'precio_anterior','precio_final','porcentaje',
        'tipo','observaciones','fecha'
    ];

    protected $casts = [
        'precio_anterior' => 'float',
        'precio_final'    => 'float',
        'porcentaje'      => 'float',
        'fecha'           => 'datetime',
    ];
}
