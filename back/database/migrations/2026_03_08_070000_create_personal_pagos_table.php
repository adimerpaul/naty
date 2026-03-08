<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personales');
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->foreignId('venta_reversion_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->string('mes', 7); // YYYY-MM
            $table->decimal('sueldo', 11, 2)->default(0);
            $table->decimal('extras', 11, 2)->default(0);
            $table->decimal('adelantos', 11, 2)->default(0);
            $table->decimal('descuentos', 11, 2)->default(0);
            $table->decimal('monto_pagado', 11, 2)->default(0);
            $table->string('estado', 20)->default('ACTIVO');
            $table->text('observacion')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->timestamps();
            $table->index(['mes', 'personal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_pagos');
    }
};

