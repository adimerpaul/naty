<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->date("fecha")->nullable();
            $table->double("total",11,2)->nullable();
            $table->double("acuenta",11,2)->nullable();
            $table->double("saldo",11,2)->nullable();
            $table->string("estado")->default('')->nullable();
            $table->string("tipo")->default('')->nullable();
            $table->string("turno")->default('')->nullable();
            $table->string("hora")->default('')->nullable();
            $table->string("fechaentrega")->default('')->nullable();
            $table->string("telefono1")->default('')->nullable();
            $table->string("telefono2")->default('')->nullable();
            $table->string("direccion")->default('')->nullable();
            $table->string("envase")->default('')->nullable();
            $table->string("observacion")->default('NINGUNO');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
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
        Schema::dropIfExists('ventas');
    }
}
