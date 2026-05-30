<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad', 50);
            $table->decimal('minimo', 12, 2)->default(0);
            $table->decimal('stock', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('razon');
            $table->string('nombre')->nullable();
            $table->string('nit', 50)->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('cantidad', 12, 2);
            $table->decimal('retiro', 12, 2)->default(0);
            $table->decimal('costo', 12, 2);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('deuda', 12, 2)->default(0);
            $table->date('fechaven')->nullable();
            $table->string('lote')->nullable();
            $table->text('comentario')->nullable();
            $table->text('observacion')->nullable();
            $table->string('estado', 50)->default('PENDIENTE');
            $table->foreignId('material_id')->constrained('materials');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('provider_id')->constrained('providers');
            $table->timestamps();
        });

        Schema::create('recuentos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('cantidad', 12, 2);
            $table->text('observacion')->nullable();
            $table->foreignId('material_id')->constrained('materials');
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('log_compras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('monto', 12, 2);
            $table->decimal('caja', 12, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->foreignId('compra_id')->constrained('compras');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_compras');
        Schema::dropIfExists('recuentos');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('providers');
        Schema::dropIfExists('materials');
    }
};
