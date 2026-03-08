<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo_venta', 20); // detalle | local
            $table->string('tipo_pago', 20)->default('contado'); // contado | credito
            $table->string('estado', 20)->default('ACTIVA');
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_direccion')->nullable();
            $table->decimal('total', 11, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};

