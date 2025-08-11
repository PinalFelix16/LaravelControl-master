<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    use HasFactory;

    // 👇 Ajusta estos nombres si en tu BD son distintos
    protected $table = 'clases';
    protected $primaryKey = 'id_clase';
    public $timestamps = true;

    protected $fillable = [
        // clave foránea que referencia a alumnos.id_alumno
        'alumno_id',
        // ... agrega aquí los demás campos de tu tabla clases ...
    ];

    public function alumno()
    {
        // FK: clases.alumno_id  →  PK: alumnos.id_alumno
        return $this->belongsTo(Alumno::class, 'alumno_id', 'id_alumno');
    }
}
