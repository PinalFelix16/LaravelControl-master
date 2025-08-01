<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('pagos', function (Blueprint $table) {
        $table->engine = 'InnoDB';
        $table->id();
        $table->unsignedBigInteger('alumno_id');
        $table->string('concepto');
        $table->decimal('monto', 8, 2);
        $table->date('fecha_pago');
        $table->string('forma_pago')->nullable();
        $table->string('referencia')->nullable();
        $table->timestamps();

        $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
