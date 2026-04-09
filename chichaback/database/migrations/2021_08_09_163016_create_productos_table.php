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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->integer("orden")->nullable();
            $table->string("nombre")->nullable()->default('');
            $table->double("precio",11,2)->nullable()->default(0);
            $table->string("observacion")->nullable();
            $table->string("color")->nullable()->default('#ffffff');
            $table->string("tipo")->nullable()->default('LOCAL');
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
        Schema::dropIfExists('productos');
    }
}
