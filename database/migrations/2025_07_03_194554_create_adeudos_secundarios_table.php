<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('adeudos_secundarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->string('concepto');
            $table->string('periodo');
            $table->decimal('monto', 8, 2)->default(0);
            $table->integer('descuento')->default(0);
            $table->integer('corte')->default(0);
            $table->timestamps();

            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adeudos_secundarios');
    }
};
