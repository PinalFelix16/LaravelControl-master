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
        'fecha_nacimiento',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'fecha_nacimiento' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

   public function clases()
    {
        return $this->hasMany(\App\Models\Clase::class, 'alumno_id', 'id_alumno');
    }
}
