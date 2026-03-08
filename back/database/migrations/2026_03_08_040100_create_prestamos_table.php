<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->default(now()->toDateString());
            $table->string('tipo', 20)->default('prestamo'); // prestamo | venta
            $table->string('estado', 30)->default('EN PRESTAMO');
            $table->decimal('efectivo', 11, 2)->default(0);
            $table->string('fisico')->nullable();
            $table->string('observacion')->nullable();
            $table->integer('cantidad')->default(1);
            $table->boolean('prestado')->default(false);
            $table->date('fechaAnulacion')->nullable();
            $table->string('motivoAnulacion')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('inventario_id')->constrained('inventarios');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};

