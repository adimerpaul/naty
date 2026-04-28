<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->date('hoja_fecha_entrega')->nullable()->after('observacion');
            $table->string('hoja_turno')->nullable()->after('hoja_fecha_entrega');
            $table->time('hoja_hora')->nullable()->after('hoja_turno');
            $table->string('hoja_telefono_1')->nullable()->after('hoja_hora');
            $table->string('hoja_telefono_2')->nullable()->after('hoja_telefono_1');
            $table->string('hoja_direccion')->nullable()->after('hoja_telefono_2');
            $table->text('hoja_envases')->nullable()->after('hoja_direccion');
            $table->decimal('hoja_cuenta', 12, 2)->nullable()->after('hoja_envases');
            $table->decimal('hoja_saldo', 12, 2)->nullable()->after('hoja_cuenta');
            $table->text('hoja_observaciones')->nullable()->after('hoja_saldo');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'hoja_fecha_entrega',
                'hoja_turno',
                'hoja_hora',
                'hoja_telefono_1',
                'hoja_telefono_2',
                'hoja_direccion',
                'hoja_envases',
                'hoja_cuenta',
                'hoja_saldo',
                'hoja_observaciones',
            ]);
        });
    }
};
