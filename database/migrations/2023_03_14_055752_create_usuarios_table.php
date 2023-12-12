<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("usuarios", function (Blueprint $table) {
            $table->id("id_usuario");
            $table
                ->string("cedula", 10)
                ->unique()
                ->index();
            $table->string("nombres");
            $table->string("apellido_p");
            $table->string("apellido_m");
            $table->enum("sexo", ["HOMBRE", "MUJER"]);
            $table->date("fecha_nacimiento");
            $table->string("celular");
            $table->string("correo_electronico")->unique();
            $table->string("clave");
            $table->enum("estado", ["A", "I"])->default("A");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("usuarios");
    }
}
