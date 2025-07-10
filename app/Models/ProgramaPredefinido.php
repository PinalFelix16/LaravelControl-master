<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaPredefinido extends Model
{
    use HasFactory;

    protected $table = 'programas_predefinidos';
    protected $primaryKey = 'id_programa';

    protected $fillable = [
        'nombre',
        'mensualidad',
        'nivel',
        'complex',
        'status',
        'ocultar',
    ];
}
