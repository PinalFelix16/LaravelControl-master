<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registro_becas', function (Blueprint $t) {
            $t->id('id_beca');
            $t->unsignedBigInteger('id_alumno');
            $t->unsignedBigInteger('id_programa');
            $t->string('periodo', 32);
            $t->decimal('precio_anterior', 10, 2);
            $t->decimal('precio_final', 10, 2);
            $t->decimal('porcentaje', 5, 2);
            $t->string('tipo', 30)->default('BECA'); // opcional (p.ej. MENSUALIDAD)
            $t->text('observaciones')->nullable();
            $t->timestamp('fecha')->useCurrent();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('registro_becas'); }
};
