<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreciosProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("precios_productos", function (Blueprint $table) {
            $table->id("id_precio_producto");

            $table->bigInteger("id_producto");
            $table
                ->foreign("id_producto")
                ->references("id_producto")
                ->on("productos");

            $table->decimal("precio_compra_sin_iva", 10, 4);
            $table->decimal("porcentaje_iva_compra", 4, 2);
            $table->decimal("precio_venta_sin_iva", 10, 4);
            $table->decimal("porcentaje_iva_venta", 4, 2);
            $table->date("fecha_actualizacion");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("precios_productos");
    }
}
