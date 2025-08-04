<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosSecundariosTable extends Migration
{
    public function up()
    {
        Schema::create('pagos_secundarios', function (Blueprint $table) {
            $table->id();
            $table->string('id_alumno', 20);
            $table->string('concepto', 30); // INSCRIPCION, RECARGO, etc.
            $table->string('periodo', 20);
            $table->decimal('monto', 8, 2);
            $table->decimal('descuento', 8, 2)->default(0);
            $table->date('fecha_pago')->nullable();
            $table->string('nomina', 20)->nullable();
            $table->string('recibo', 20)->nullable();
            $table->string('corte', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos_secundarios');
    }
}
