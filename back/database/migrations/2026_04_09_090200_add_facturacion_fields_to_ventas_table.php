<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'facturado')) {
                $table->boolean('facturado')->default(false)->after('deuda_oculta');
            }
            if (!Schema::hasColumn('ventas', 'factura_estado')) {
                $table->string('factura_estado', 30)->default('SIN_GESTION')->after('facturado');
            }
            if (!Schema::hasColumn('ventas', 'factura_error')) {
                $table->text('factura_error')->nullable()->after('factura_estado');
            }
            if (!Schema::hasColumn('ventas', 'cuf')) {
                $table->string('cuf')->nullable()->after('factura_error');
            }
            if (!Schema::hasColumn('ventas', 'cufd')) {
                $table->string('cufd')->nullable()->after('cuf');
            }
            if (!Schema::hasColumn('ventas', 'leyenda')) {
                $table->text('leyenda')->nullable()->after('cufd');
            }
            if (!Schema::hasColumn('ventas', 'online')) {
                $table->boolean('online')->default(false)->after('leyenda');
            }
            if (!Schema::hasColumn('ventas', 'siat_codigo_recepcion')) {
                $table->string('siat_codigo_recepcion')->nullable()->after('online');
            }
            if (!Schema::hasColumn('ventas', 'factura_xml_path')) {
                $table->string('factura_xml_path')->nullable()->after('siat_codigo_recepcion');
            }
            if (!Schema::hasColumn('ventas', 'factura_gz_path')) {
                $table->string('factura_gz_path')->nullable()->after('factura_xml_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            foreach ([
                'facturado',
                'factura_estado',
                'factura_error',
                'cuf',
                'cufd',
                'leyenda',
                'online',
                'siat_codigo_recepcion',
                'factura_xml_path',
                'factura_gz_path',
            ] as $column) {
                if (Schema::hasColumn('ventas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
