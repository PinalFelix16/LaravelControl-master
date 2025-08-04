<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoFragmentado extends Model
{
    use HasFactory;

    protected $table = 'pagos_fragmentados';

    protected $fillable = [
        'id_alumno',
        'id_programa',
        'id_clase',
        'periodo',
        'id_maestro',
        'monto',
        'nomina',
    ];

    // Relaciones (ajusta si tus modelos existen y los nombres de clave foránea)
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id');
    }
    public function programa()
    {
        return $this->belongsTo(ProgramaPredefinido::class, 'id_programa', 'id');
    }
    public function clase()
    {
        return $this->belongsTo(Clase::class, 'id_clase', 'id');
    }
    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'id_maestro', 'id');
    }
}
