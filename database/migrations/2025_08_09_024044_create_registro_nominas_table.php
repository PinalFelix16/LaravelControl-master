<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registro_nominas', function (Blueprint $table) {
            $table->bigIncrements('id_registro');
            $table->unsignedBigInteger('id_nomina');
            $table->unsignedBigInteger('id_maestro');
            $table->unsignedBigInteger('id_clase');
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('comision', 10, 2)->default(0);
            $table->decimal('total_neto', 10, 2)->default(0);

            $table->index('id_nomina');
        });
    }

    public function down(): void {
        Schema::dropIfExists('registro_nominas');
    }
};
