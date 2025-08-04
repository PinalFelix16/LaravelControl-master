<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    use HasFactory;

    protected $table = 'maestros';
    protected $primaryKey = 'id_maestro';  // <--- Importante
    public $incrementing = true;           // <--- Importante
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'nombre_titular',
        'direccion',
        'fecha_nac',
        'rfc',
        'celular',
        'status'
    ];
}
