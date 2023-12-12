<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("proveedores", function (Blueprint $table) {
            $table->id("id_proveedor");
            $table->string("nombre");
            $table->string("direccion");
            $table->string("telefono", 9);
            $table->string("celular", 10);
            $table->string("correo_electronico");
            $table->text("descripcion")->nullable(true);
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
        Schema::dropIfExists("proveedores");
    }
}
