<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'local')) {
                $table->string('local')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('clientes', 'titular')) {
                $table->string('titular')->nullable()->after('local');
            }
            if (!Schema::hasColumn('clientes', 'tipo')) {
                $table->string('tipo')->nullable()->after('titular');
            }
            if (!Schema::hasColumn('clientes', 'legalidad')) {
                $table->string('legalidad')->nullable()->after('fechanac');
            }
            if (!Schema::hasColumn('clientes', 'categoria')) {
                $table->string('categoria')->nullable()->after('legalidad');
            }
            if (!Schema::hasColumn('clientes', 'razon')) {
                $table->string('razon')->nullable()->after('categoria');
            }
            if (!Schema::hasColumn('clientes', 'nit')) {
                $table->string('nit')->nullable()->after('razon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $drops = [];
            foreach (['local', 'titular', 'tipo', 'legalidad', 'categoria', 'razon', 'nit'] as $col) {
                if (Schema::hasColumn('clientes', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};

