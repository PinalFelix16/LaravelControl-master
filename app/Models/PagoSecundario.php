<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoSecundario extends Model
{
    use HasFactory;

    protected $table = 'pagos_secundarios';

    protected $fillable = [
        'id_alumno',
        'concepto',
        'periodo',
        'monto',
        'descuento',
        'fecha_pago',
        'nomina',
        'recibo',
        'corte',
    ];

    // Relaciones Eloquent (ajusta si tienes modelos y claves foráneas)
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id');
    }
}
