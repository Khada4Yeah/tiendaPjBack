<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("clientes", function (Blueprint $table) {
            $table->id("id_cliente");

            $table->bigInteger("id_usuario");
            $table
                ->foreign("id_usuario")
                ->references("id_usuario")
                ->on("usuarios");

            $table->string("direccion_envio");
            $table->string("informacion_pago");
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
        Schema::dropIfExists("clientes");
    }
}
