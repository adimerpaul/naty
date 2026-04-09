<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuis', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->dateTime('fechaVigencia')->nullable();
            $table->dateTime('fechaCreacion')->nullable();
            $table->integer('codigoPuntoVenta')->default(0);
            $table->integer('codigoSucursal')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuis');
    }
};
