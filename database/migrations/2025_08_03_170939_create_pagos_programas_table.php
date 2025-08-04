<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosProgramasTable extends Migration
{
    public function up()
    {
        Schema::create('pagos_programas', function (Blueprint $table) {
            $table->id(); // id autoincremental
            $table->string('id_alumno', 20); // ajusta tamaño si es necesario
            $table->string('id_programa', 10);
            $table->string('periodo', 20);
            $table->string('concepto', 30); // MENSUALIDAD, VISITA, etc.
            $table->decimal('monto', 8, 2);
            $table->decimal('descuento', 8, 2)->default(0);
            $table->decimal('beca', 8, 2)->default(0);
            $table->date('fecha_limite')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->string('recibo', 20)->nullable();
            $table->string('corte', 20)->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos_programas');
    }
}
