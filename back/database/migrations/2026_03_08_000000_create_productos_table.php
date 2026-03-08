<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('precio', 11, 2)->default(0);
            $table->string('observacion')->nullable();
            $table->string('grupo', 100)->nullable(); // chicha | garapina
            $table->string('tipo_producto', 20); // detalle | local
            $table->integer('orden')->default(1);
            $table->string('color', 20)->default('#ffffff');
            $table->string('fotografia')->nullable();
            $table->boolean('estado')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

