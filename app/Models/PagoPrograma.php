<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoPrograma extends Model
{
    use HasFactory;

    protected $table = 'pagos_programas'; // Nombre exacto de la tabla

    // Campos que puedes asignar masivamente
    protected $fillable = [
        'id_alumno',
        'id_programa',
        'periodo',
        'concepto',
        'monto',
        'descuento',
        'beca',
        'fecha_limite',
        'fecha_pago',
        'recibo',
        'corte',
    ];

    // --- Relaciones Eloquent (si quieres) ---
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id');
    }

    public function programa()
    {
        return $this->belongsTo(ProgramaPredefinido::class, 'id_programa', 'id');
    }
}
