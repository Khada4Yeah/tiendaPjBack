<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtributosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("atributos", function (Blueprint $table) {
            $table->id("id_atributo");

            $table->bigInteger("id_categoria");
            $table
                ->foreign("id_categoria")
                ->references("id_categoria")
                ->on("categorias");

            $table->string("nombre");
            $table->string("unidad_de_medida");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("atributos");
    }
}
