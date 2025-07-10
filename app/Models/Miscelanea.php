<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miscelanea extends Model
{
    protected $table = 'miscelanea';
    protected $fillable = ['descripcion','monto','corte'];
}
