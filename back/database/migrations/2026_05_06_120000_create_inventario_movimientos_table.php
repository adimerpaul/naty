<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('fecha');
            $table->string('tipo', 20); // AUMENTO | DISMINUCION
            $table->string('estado', 20)->default('REGISTRADO'); // REGISTRADO | ANULADO
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('cantidad_anterior');
            $table->unsignedInteger('cantidad_nueva');
            $table->string('motivo', 255)->nullable();
            $table->timestamp('anulado_at')->nullable();
            $table->foreignId('anulado_por')->nullable()->constrained('users');
            $table->string('motivo_anulacion', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};

