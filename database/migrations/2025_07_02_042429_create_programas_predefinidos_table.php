<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programas_predefinidos', function (Blueprint $table) {
            $table->id('id_programa');
            $table->string('nombre')->unique();
            $table->decimal('mensualidad', 8, 2)->default(0);
            $table->string('nivel')->nullable();
            $table->boolean('complex')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('ocultar')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas_predefinidos');
    }
};
