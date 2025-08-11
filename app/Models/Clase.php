<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    // ⬇️ Ajusta SOLO si tu tabla/PK son distintos
    protected $table = 'clases';
    protected $primaryKey = 'id_clase';   // <-- si tu PK es 'id', cámbialo a 'id'
    public $incrementing = true;
    protected $keyType = 'int';

    // Si tu tabla NO tiene created_at/updated_at, deja false
    public $timestamps = false;

    protected $fillable = [
        // agrega tus columnas cuando vayas a crear/editar
        // 'alumno_id', 'maestro_id', 'materia', 'status', 'hora_inicio', 'hora_fin'
    ];

    // Relaciones (descomenta/ajusta si las usas)
    // public function alumno()
    // {
    //     return $this->belongsTo(\App\Models\Alumno::class, 'alumno_id', 'id_alumno');
    // }
}
