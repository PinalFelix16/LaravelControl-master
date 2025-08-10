<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('adeudos_programas')) {
            Schema::table('adeudos_programas', function (Blueprint $t) {
                if (!Schema::hasColumn('adeudos_programas', 'monto'))     $t->decimal('monto', 10, 2)->default(0);
                if (!Schema::hasColumn('adeudos_programas', 'beca'))      $t->decimal('beca', 5, 2)->default(0);
                if (!Schema::hasColumn('adeudos_programas', 'descuento')) $t->decimal('descuento', 5, 2)->default(0);
                if (!Schema::hasColumn('adeudos_programas', 'periodo'))   $t->string('periodo', 32)->default('');
            });
        }

        if (Schema::hasTable('adeudos_fragmentados')) {
            Schema::table('adeudos_fragmentados', function (Blueprint $t) {
                if (!Schema::hasColumn('adeudos_fragmentados', 'porcentaje')) $t->decimal('porcentaje', 6, 3)->default(0);
                if (!Schema::hasColumn('adeudos_fragmentados', 'monto'))      $t->decimal('monto', 10, 2)->default(0);
                if (!Schema::hasColumn('adeudos_fragmentados', 'periodo'))    $t->string('periodo', 32)->default('');
            });
        }
    }

    public function down(): void {
        // no se eliminan columnas para no romper datos existentes
    }
};
