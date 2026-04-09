<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string("codigo")->nullable();
            $table->date("fecha")->default(date('Y-m-d'));
            $table->string("nombre")->nullable();
            $table->integer("cantidad")->default(0);
            $table->double("precio")->default(0);
            $table->string("detalle")->nullable();
            $table->string("estado")->default('ACTIVO');
//            $table->unsignedBigInteger('producto_id')->nullable();
//            $table->foreign('producto_id')->references('id')->on('productos');
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
        Schema::dropIfExists('inventarios');
    }
}
