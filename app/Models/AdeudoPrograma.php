<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdeudoPrograma extends Model
{
    use HasFactory;

    protected $table = 'adeudos_programas';

    protected $fillable = [
        'id_alumno',
        'id_programa',
        'periodo',
        'concepto',
        'monto',
        'beca',
        'descuento',
        'fecha_limite',
    ];

    // Relaciones (ajusta si tienes modelos para alumno y programa)
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id');
    }
    public function programa()
    {
        return $this->belongsTo(ProgramaPredefinido::class, 'id_programa', 'id');
    }
}
