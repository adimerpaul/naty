<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarantiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garantias', function (Blueprint $table) {
            $table->id();
            $table->date("fecha")->nullable()->default(date('Y-m-d'));
            $table->double("efectivo",11,2)->nullable()->default(0);
            $table->string("fisico")->nullable()->default('');
            $table->string("observacion")->nullable()->default('');
            $table->integer("cantidad")->nullable();
            $table->string("estado")->nullable();
            $table->date("fechadev")->nullable();
            $table->unsignedBigInteger('userdev_id')->nullable();
            $table->foreign('userdev_id')->references('id')->on('users');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('inventario_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');
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
        Schema::dropIfExists('garantias');
    }
}
