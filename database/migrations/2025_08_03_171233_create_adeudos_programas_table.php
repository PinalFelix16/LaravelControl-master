<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdeudosProgramasTable extends Migration
{
    public function up()
    {
        Schema::create('adeudos_programas', function (Blueprint $table) {
            $table->id();
            $table->string('id_alumno', 20);
            $table->string('id_programa', 10);
            $table->string('periodo', 20);
            $table->string('concepto', 30); // MENSUALIDAD, VISITA, etc.
            $table->decimal('monto', 8, 2);
            $table->decimal('beca', 8, 2)->default(0);
            $table->decimal('descuento', 8, 2)->default(0);
            $table->date('fecha_limite')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adeudos_programas');
    }
}
