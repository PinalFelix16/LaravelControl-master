<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// app/Models/RegistroBeca.php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class RegistroBeca extends Model {
  protected $table = 'registro_becas';
  protected $primaryKey = 'id_beca';
  protected $fillable = ['id_alumno','id_programa','periodo','precio_anterior','precio_final','porcentaje','tipo','observaciones','fecha'];
}

// app/Models/RegistroDescuento.php
/*namespace App\Models; use Illuminate\Database\Eloquent\Model;
class RegistroDescuento extends Model {
  protected $table = 'registro_descuentos';
  protected $primaryKey = 'id_descuento';
  protected $fillable = ['id_alumno','id_programa','periodo','precio_anterior','precio_final','porcentaje','tipo','observaciones','fecha'];
}*/

// app/Models/AdeudoPrograma.php
/*namespace App\Models; use Illuminate\Database\Eloquent\Model;
class AdeudoPrograma extends Model {
  protected $table = 'adeudos_programas';
  public $timestamps = false;
  protected $fillable = ['id_alumno','id_programa','periodo','monto','beca','descuento'];
}*/

// app/Models/AdeudoFragmentado.php
/*namespace App\Models; use Illuminate\Database\Eloquent\Model;
class AdeudoFragmentado extends Model {
  protected $table = 'adeudos_fragmentados';
  public $timestamps = false;
  protected $fillable = ['id_alumno','id_programa','periodo','id_maestro','id_clase','porcentaje','monto'];
}*/
