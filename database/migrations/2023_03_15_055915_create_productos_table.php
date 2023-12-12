<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("productos", function (Blueprint $table) {
            $table->id("id_producto");

            $table->bigInteger("id_categoria");
            $table
                ->foreign("id_categoria")
                ->references("id_categoria")
                ->on("categorias");

            $table->bigInteger("id_proveedor");
            $table
                ->foreign("id_proveedor")
                ->references("id_proveedor")
                ->on("proveedores");

            $table->bigInteger("id_marca");
            $table
                ->foreign("id_marca")
                ->references("id_marca")
                ->on("marcas");

            $table
                ->bigInteger("EAN")
                ->unique()
                ->index();
            $table->string("codigo")->unique();
            $table->string("nombre");
            $table->text("descripcion");
            $table->string("modelo");
            $table->string("ruta_imagen");
            $table->enum("estado", ["A", "I"])->default("A");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("productos");
    }
}
