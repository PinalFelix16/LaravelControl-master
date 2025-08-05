<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $fillable = [
        'alumno_id',
        'concepto',
        'monto',
        'fecha_pago',
        'forma_pago',
        'referencia'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
