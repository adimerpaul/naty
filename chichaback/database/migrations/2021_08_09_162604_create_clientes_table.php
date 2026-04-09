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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string("local")->nullable()->default('');
            $table->string("ci")->unique();
            $table->string("titular")->nullable()->default('');
            $table->string("tipo")->nullable()->default('');
            $table->string("telefono")->nullable()->default('');
            $table->date("fechanac")->nullable();
            $table->string("direccion")->nullable()->default('');
            $table->string("legalidad")->nullable()->default('');
            $table->string("categoria")->nullable()->default('');
            $table->string("razon")->nullable()->default('');
            $table->string("nit")->nullable()->default('');
            $table->string("observacion")->nullable()->default('');
            $table->boolean('tipocliente')->default(true);
            $table->string("estado")->nullable()->default('ACTIVO');
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
        Schema::dropIfExists('clientes');
    }
}
