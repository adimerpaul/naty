<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personales', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('nombre');
            $table->decimal('salario', 11, 2)->default(0);
            $table->date('fechanac')->nullable();
            $table->integer('dias')->default(0);
            $table->string('celular')->nullable();
            $table->string('tipo')->nullable();
            $table->date('fechaingreso')->nullable();
            $table->string('estado', 100)->default('ACTIVO');
            $table->string('fotografia')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personales');
    }
};

