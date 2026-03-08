<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('nro_cuota')->default(1);
            $table->decimal('monto', 11, 2);
            $table->date('fecha_programada')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->string('metodo', 30)->nullable();
            $table->string('estado', 20)->default('PENDIENTE'); // PENDIENTE | PAGADO
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

