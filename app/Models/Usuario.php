<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /** Si tu tabla no maneja created_at/updated_at */
    public $timestamps = false;

    /** Tabla y clave primaria */
    protected $table = 'usuarios';

    /**
     * Si tu PK es de TEXTO (por ej. varchar) deja estas 2 líneas.
     * Si en tu BD la PK es INT autoincremental, cambia a:
     *   public $incrementing = true;
     *   protected $keyType   = 'int';
     */
    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    /** Asignación masiva */
    protected $fillable = [
        'id',
        'usuario',
        'nombre',
        'password',
        'permisos',   // si es JSON en BD, ver $casts abajo
    ];

    /** Ocultar en JSON */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Casts útiles */
    protected $casts = [
        // Si la columna permisos es JSON en la BD, descomenta:
        // 'permisos' => 'array',
    ];

    /**
     * Mutator: si te pasan un password en texto plano
     * lo guarda hasheado. Si ya viene hasheado (bcrypt),
     * lo deja tal cual.
     */
    public function setPasswordAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['password'] = $value;
            return;
        }

        // patrón típico de bcrypt ($2y$10$... 60 chars)
        $isBcrypt = is_string($value) && preg_match('/^\$2y\$\d{2}\$.{53}$/', $value);

        $this->attributes['password'] = $isBcrypt ? $value : bcrypt($value);
    }

    /**
     * Solo si en algún momento usas los traits de auth de Laravel
     * que leen este método (no afecta tu AuthController actual).
     */
    public function username(): string
    {
        return 'usuario';
    }
}
