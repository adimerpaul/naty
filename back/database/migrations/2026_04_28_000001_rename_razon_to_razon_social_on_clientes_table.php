<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'razon') && !Schema::hasColumn('clientes', 'razon_social')) {
                $table->renameColumn('razon', 'razon_social');
            } elseif (!Schema::hasColumn('clientes', 'razon_social')) {
                $table->string('razon_social')->nullable()->after('categoria');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'razon_social')) {
                $table->renameColumn('razon_social', 'razon');
            }
        });
    }
};
