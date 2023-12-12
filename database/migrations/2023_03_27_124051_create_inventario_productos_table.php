<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("inventario_productos", function (Blueprint $table) {
            $table->id("id_inventario_producto");

            $table->bigInteger("id_producto");
            $table
                ->foreign("id_producto")
                ->references("id_producto")
                ->on("productos");

            $table->bigInteger("id_precio_producto");
            $table
                ->foreign("id_precio_producto")
                ->references("id_precio_producto")
                ->on("precios_productos");

            $table->integer("cantidad", false, true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("inventario_productos");
    }
}
