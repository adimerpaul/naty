<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planillas', function (Blueprint $table) {
            $table->id();
            $table->date('fechainicio')->nullable();
            $table->date('fechafin')->nullable();
            $table->date('fechapago')->nullable();
            $table->double('monto',11,2);
            $table->double('adelanto',11,2);
            $table->double('descuento',11,2);
            $table->double('bono',11,2);
            $table->double('restante',11,2);
            $table->double('total',11,2);
            $table->string('observacion')->nullable();
            $table->string('estado')->default('CREADO');
            $table->unsignedBigInteger('empleado_id');
            $table->foreign('empleado_id')->references('id')->on('empleados');
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
        Schema::dropIfExists('planillas');
    }
}
