<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->date('fecha')->default(now()->toDateString());
            $table->string('nombre');
            $table->integer('cantidad')->default(0);
            $table->string('detalle')->nullable();
            $table->integer('orden')->default(0);
            $table->string('estado', 30)->default('ACTIVO');
            $table->decimal('precio', 11, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};

