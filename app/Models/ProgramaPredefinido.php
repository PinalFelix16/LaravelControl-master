<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*class ProgramaPredefinido extends Model
{
    protected $table = 'programas_predefinidos';
    protected $primaryKey = 'id_programa';
    public $incrementing = true;   // si tu id_programa es INT autoincremental
    protected $keyType = 'int';    // si fuera '001' string, cambia a 'string' y $incrementing=false
    public $timestamps = true;

    protected $fillable = ['nombre','mensualidad','nivel','complex','status','ocultar'];

    // Clases que usan este programa
    public function clases()
    {
        return $this->hasMany(Clase::class, 'id_programa', 'id_programa');
    }
}
*/
