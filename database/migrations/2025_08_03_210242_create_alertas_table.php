<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('alertas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_alumno');
        $table->string('mensaje');
        $table->string('tipo')->default('recargo'); // Puedes tener otros tipos: recargo, adeudo, etc.
        $table->boolean('leido')->default(false);   // Para saber si el usuario ya vio la alerta
        $table->timestamps();

        // Relación foránea (ajusta si tu PK de alumnos es diferente)
        $table->foreign('id_alumno')->references('id')->on('alumnos')->onDelete('cascade');
    });
}

};
