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
    Schema::create('clases', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('alumno_id');
        $table->string('nombre_clase');
        $table->string('nivel');
        $table->string('grupo');
        $table->string('turno')->nullable();
        $table->decimal('costo', 8, 2)->default(0);
        $table->string('estatus')->default('activa');
        $table->timestamps();

        $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clases');
    }
};
