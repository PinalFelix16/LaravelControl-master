<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Indica la tabla personalizada
    protected $table = 'usuarios';

    // Tu clave primaria es tipo string
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'id',
        'usuario',
        'nombre',
        'password',
        'permisos',
    ];

    // Oculta el password cuando serializas a JSON
    protected $hidden = [
        'password',
    ];

    // Si quieres, puedes agregar otros métodos o relaciones aquí

    // Si deseas sobreescribir el nombre de usuario para autenticación:
    public function username()
    {
        return 'usuario'; // <--- este es tu campo de login
    }
}
