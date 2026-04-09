<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogplanillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logplanillas', function (Blueprint $table) {
            $table->id();
            $table->string("tipo");
            $table->string("monto");
            $table->string("motivo");
            $table->date("fecha");
            $table->time("hora");
            $table->unsignedBigInteger('planilla_id');
            $table->foreign('planilla_id')->references('id')->on('planillas')->onDelete('cascade');
//            $table->unsignedBigInteger('planilla_id');
//            $table->foreign('planilla_id')->references('id')->on('planillas');
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
        Schema::dropIfExists('logplanillas');
    }
}
