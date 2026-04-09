<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permisos')->insert([
            ["id"=>1, "nombre"=>"Controlar usuarios","orden"=>100,"descripcion"=>"Esta opcion permite controlar los usuarios del sistema"],
            ["id"=>2, "nombre"=>"Controlar clientes","orden"=>200,"descripcion"=>"Esta opcion permite controlar los clientes del sistema"],
            ["id"=>3, "nombre"=>"Controlar productos","orden"=>300,"descripcion"=>"Esta opcion permite controlar los productos del sistema"],
            ["id"=>4, "nombre"=>"Controlar inventario","orden"=>500,"descripcion"=>"Esta opcion permite controlar el inventario del sistema"],
            ["id"=>5, "nombre"=>"Venta detalle","orden"=>700,"descripcion"=>"Esta opcion permite realizar ventas en detalle"],
            ["id"=>6, "nombre"=>"Ver historial detalle","orden"=>800,"descripcion"=>"En esta opcion se puede ver el historial de ventas en detalle"],
            ["id"=>7, "nombre"=>"Venta local","orden"=>1000,"descripcion"=>"Esta opcion permite realizar ventas en local"],
            ["id"=>8, "nombre"=>"Ver historial local","orden"=>1100,"descripcion"=>"En esta opcion se puede ver el historial de ventas en local"],
            ["id"=>9, "nombre"=>"Controlar empleado","orden"=>200,"descripcion"=>"Esta opcion permite controlar los empleados del sistema"],
            ["id"=>10, "nombre"=>"Reporte Usuario","orden"=>2800,"descripcion"=>"Esta opcion permite ver el reporte de usuarios"],
            ["id"=>11, "nombre"=>"Controlar gastos","orden"=>1800,"descripcion"=>"Esta opcion permite controlar los gastos del sistema"],
            ["id"=>12, "nombre"=>"Historial prestamos","orden"=>1500,"descripcion"=>"Esta opcion permite ver el historial de prestamos"],
            ["id"=>13, "nombre"=>"Historial venta","orden"=>2900,"descripcion"=>"Esta opcion permite ver el historial de ventas"],
            ["id"=>14, "nombre"=>"Historial Anular","orden"=>300,"descripcion"=>"Esta opcion permite ver el historial de anulaciones"],
            ["id"=>15, "nombre"=>"Generar Hoja Ruta","orden"=>1300,"descripcion"=>"Esta opcion permite generar hoja de ruta"],
            ["id"=>16, "nombre"=>"Anular Prestamo","orden"=>1600,"descripcion"=>"Esta opcion permite anular prestamos"],
            ["id"=>17, "nombre"=>"Reimprimir","orden"=>3100,"descripcion"=>"Esta opcion permite reimprimir"],
            ["id"=>18, "nombre"=>"Eliminar gastos","orden"=>1900,"descripcion"=>"Esta opcion permite eliminar gastos"],
            ["id"=>19, "nombre"=>"Cuentas por cobrar detalle","orden"=>900,"descripcion"=>"Esta opcion permite ver las cuentas por cobrar en detalle"],
            ["id"=>20, "nombre"=>"Cuentas por cobrar local","orden"=>1200,"descripcion"=>"Esta opcion permite ver las cuentas por cobrar en local"],
            ["id"=>21, "nombre"=>"Cuentas por cobrar ruta","orden"=>1400,"descripcion"=>"Esta opcion permite ver las cuentas por cobrar en ruta"],
            ["id"=>22, "nombre"=>"Caja Chica","orden"=>2600,"descripcion"=>"Esta opcion permite ver la caja chica"],
            ["id"=>23, "nombre"=>"Editar Almacen","orden"=>2300,"descripcion"=>"Esta opcion permite editar el almacen"],
            ["id"=>24, "nombre"=>"Editar Producto","orden"=>400,"descripcion"=>"Esta opcion permite editar el producto"],
            ["id"=>25, "nombre"=>"Editar Inventario","orden"=>600,"descripcion"=>"Esta opcion permite editar el inventario"],
            ["id"=>26, "nombre"=>"Editar Glosa","orden"=>3200,"descripcion"=>"Esta opcion permite editar la glosa"],
            ["id"=>27, "nombre"=>"Eliminar Prestamo","orden"=>1700,"descripcion"=>"Esta opcion permite eliminar prestamos"],
            ["id"=>28, "nombre"=>"Ver Almacen","orden"=>2200,"descripcion"=>"Esta opcion permite ver el almacen"],
            ["id"=>29, "nombre"=>"Pagos Almacen","orden"=>2400,"descripcion"=>"Esta opcion permite ver los pagos del almacen"],
            ["id"=>30, "nombre"=>"Caja General","orden"=>2500,"descripcion"=>"Esta opcion permite ver la caja general"],
            ["id"=>31, "nombre"=>"Reporte Gasto User","orden"=>2700,"descripcion"=>"Esta opcion permite ver el reporte de gastos por usuario"],
            ["id"=>32, "nombre"=>"Historial Pago Empleado","orden"=>2100,"descripcion"=>"Esta opcion permite ver el historial de pagos de empleados"],
            ["id"=>33, "nombre"=>"Resumen Contable","orden"=>2600,"descripcion"=>"Esta opcion permite ver el resumen contable"],
            ["id"=>34, "nombre"=>"Eliminar pago","orden"=>3300,"descripcion"=>"Esta opcion permite eliminar pagos"],
            ["id"=>35, "nombre"=>"Precio Unitario","orden"=>3400,"descripcion"=>"Esta opcion permite ver el precio unitario"],
            ["id"=>36, "nombre"=>"Ingreso de material","orden"=>3400,"descripcion"=>"Esta opcion permite ver el ingreso de material"],
            ["id"=>37, "nombre"=>"Egreso de material","orden"=>3400,"descripcion"=>"Esta opcion permite ver el egreso de material"],
        ]);
    }
}
