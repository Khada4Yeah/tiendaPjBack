<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("administradores", function (Blueprint $table) {
            $table->id("id_administrador");

            $table->bigInteger("id_usuario");
            $table
                ->foreign("id_usuario")
                ->references("id_usuario")
                ->on("usuarios");
            // ->onDelete("cascade")
            // ->onUpdate("cascade");

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
        Schema::dropIfExists("administradores");
    }
}
