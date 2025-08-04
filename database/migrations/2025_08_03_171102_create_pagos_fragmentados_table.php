<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosFragmentadosTable extends Migration
{
    public function up()
    {
        Schema::create('pagos_fragmentados', function (Blueprint $table) {
            $table->id();
            $table->string('id_alumno', 20);
            $table->string('id_programa', 10);
            $table->string('id_clase', 10);
            $table->string('periodo', 20);
            $table->string('id_maestro', 20);
            $table->decimal('monto', 8, 2);
            $table->string('nomina', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos_fragmentados');
    }
}
