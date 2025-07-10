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
        Schema::create('registro_predefinido', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_programa');
            $table->decimal('precio', 8, 2)->default(0);
            $table->integer('beca')->default(0);
            $table->timestamps();

            $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('id_programa')->references('id_programa')->on('programas_predefinidos')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_predefinido');
    }
};
