<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    public $timestamps = false;
    protected $table = 'programas_predefinidos';
    protected $primaryKey = 'id_programa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_programa',
        'nombre',
        'mensualidad',
        'nivel',
        'complex',
        'status',
        'ocultar'
    ];
}
