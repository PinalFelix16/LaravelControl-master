<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    // Tabla y PK reales
    protected $table = 'programas_predefinidos';
    protected $primaryKey = 'id_programa';

    // En tu BD es INT autoincremental
    public $incrementing = true;
    protected $keyType = 'int';

    // Si no quieres que Eloquent maneje created_at/updated_at
    public $timestamps = false;

    // Campos permitidos para mass-assignment (NO incluyas la PK autoincremental)
    protected $fillable = [
        'nombre',
        'mensualidad',
        'nivel',
        'complex',
        'status',
        'ocultar',
    ];

    // Casts útiles para respuestas JSON coherentes
    protected $casts = [
        'id_programa' => 'int',
        'mensualidad' => 'decimal:2',
        'complex'     => 'int',
        'status'      => 'int',
        'ocultar'     => 'int',
        // Si algún día activas timestamps, estos te sirven:
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // Relación: un programa tiene muchas clases
    public function clases()
    {
        return $this->hasMany(Clase::class, 'id_programa', 'id_programa');
    }
}
