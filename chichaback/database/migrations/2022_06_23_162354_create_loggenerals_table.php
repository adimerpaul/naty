<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loggenerals', function (Blueprint $table) {
            $table->id();
            $table->integer('numero')->default(0);
            $table->double('monto')->default(0);
            $table->string('detalle')->nullable();
            $table->string('motivo')->nullable();
            $table->string('tipo')->default('AGREGA');
            $table->date('fecha');
            $table->time('hora');
            $table->unsignedBigInteger('glosa_id')->nullable();
            $table->foreign('glosa_id')->references('id')->on('glosas');  
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');  
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
        Schema::dropIfExists('loggenerals');
    }
}
