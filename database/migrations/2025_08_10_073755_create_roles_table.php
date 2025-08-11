<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
        return; // ya existe, no intentes crearla de nuevo
    }

    \Illuminate\Support\Facades\Schema::create('roles', function (\Illuminate\Database\Schema\Blueprint $t) {
        $t->id('id_rol');
        $t->string('nombre', 60)->unique();
        $t->string('descripcion', 255)->nullable();
        $t->timestamps();
    });
}
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
