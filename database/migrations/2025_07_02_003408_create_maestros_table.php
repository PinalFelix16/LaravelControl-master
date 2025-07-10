<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maestros', function (Blueprint $table) {
            $table->id('id_maestro');
            $table->string('nombre');
            $table->string('nombre_titular')->nullable();
            $table->string('direccion')->nullable();
            $table->date('fecha_nac')->nullable();
            $table->string('rfc')->nullable();
            $table->string('celular')->nullable();
            $table->string('status')->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maestros');
    }
};
