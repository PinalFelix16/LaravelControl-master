<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Programa;

class Clase extends Model
{
    protected $table = 'clases';
    protected $primaryKey = 'id_clase';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_programa',
        'alumno_id',
        'nombre',
        'id_maestro',
        'id_maestro_2',
        'informacion',
        'lugar',
        'hora_inicio',
        'hora_fin',
        'dias',          // CSV en BD, array en API (ver accessor/mutator)
        'mensualidad',
        'complejo',
        'porcentaje',
        'personal',
        // 'status', // si lo usas para soft-delete lógico (1/0)
    ];

    protected $casts = [
        'id_programa' => 'integer',
        'alumno_id'   => 'integer',
        'mensualidad' => 'float',
        'porcentaje'  => 'float',
        'personal'    => 'integer',
        'complejo'    => 'integer',
    ];

    // --- Dias: CSV <-> array ---
    public function getDiasAttribute($value)
    {
        if ($value === null || $value === '') return [];
        return explode(',', $value);
    }

    public function setDiasAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['dias'] = implode(',', $value);
        } else {
            $this->attributes['dias'] = $value; // asume CSV/string ya
        }
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'id_programa', 'id_programa');
    }

    // Para exponer directamente el nombre del programa en el JSON
    protected $appends = ['programa_nombre'];

    public function getProgramaNombreAttribute()
    {
        return optional($this->programa)->nombre;
    }
}
