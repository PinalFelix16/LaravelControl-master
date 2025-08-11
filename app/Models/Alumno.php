<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'alumnos';
    protected $primaryKey = 'id_alumno';
    public $incrementing = true;
    protected $keyType = 'int';
    // public $timestamps = true; // Eloquent ya lo asume por defecto

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'celular',
        'telefono',
        'telefono_2',
        'tutor',
        'tutor_2',
        'hist_medico',
        'beca',
        'fecha_nacimiento',   //  nombre correcto según tu BD
        'status',             // 0/1 en tu BD
    ];

    protected $casts = [
        'status' => 'boolean',
        'fecha_nacimiento' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    // Relación: un alumno puede tener muchas clases
    public function clases()
    {
        // FK en clases = alumno_id ; PK local = id_alumno
        return $this->hasMany(\App\Models\Clase::class, 'alumno_id', 'id_alumno');
    }
}
