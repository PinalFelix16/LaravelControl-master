<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdeudosSecundariosTable extends Migration
{
    public function up()
    {
        Schema::create('adeudos_secundarios', function (Blueprint $table) {
            $table->id();
            $table->string('id_alumno', 20);
            $table->string('concepto', 30); // RECARGO, INSCRIPCION, etc.
            $table->string('periodo', 20);
            $table->decimal('monto', 8, 2);
            $table->decimal('descuento', 8, 2)->default(0);
            $table->string('corte', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adeudos_secundarios');
    }
}
