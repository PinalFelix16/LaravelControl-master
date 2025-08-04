<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clases', function (Blueprint $table) {
            $table->integer('id_programa')->unsigned();
            $table->increments('id_clase'); // Primary Key
            $table->bigInteger('alumno_id')->unsigned();
            $table->string('nombre', 30);
            $table->string('id_maestro', 6);
            $table->string('informacion', 100);
            $table->float('porcentaje', 4, 1);
            $table->integer('personal')->unsigned();
            // Si necesitas timestamps (created_at, updated_at) descomenta la siguiente línea:
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('clases');
    }
}
