<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2025_08_10_000004_create_roles_table.php
return new class extends Migration {
  public function up(): void {
    Schema::create('roles', function (Blueprint $t) {
      $t->id('id_rol');
      $t->string('nombre', 60)->unique();
      $t->string('descripcion', 255)->nullable();
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('roles'); }
};
