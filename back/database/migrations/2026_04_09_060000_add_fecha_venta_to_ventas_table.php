<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->date('fecha_venta')->nullable()->after('estado');
        });

        DB::statement("UPDATE ventas SET fecha_venta = DATE(created_at) WHERE fecha_venta IS NULL");
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('fecha_venta');
        });
    }
};
