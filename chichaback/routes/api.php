<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('/login',[\App\Http\Controllers\UserController::class,'login']);

Route::group(['middleware'=>'auth:sanctum'],function (){
    Route::post('/logout',[\App\Http\Controllers\UserController::class,'logout']);
    Route::post('/me',[\App\Http\Controllers\UserController::class,'me']);
    Route::post('listuser',[\App\Http\Controllers\UserController::class,'listuser']);

    Route::resource('/cliente',\App\Http\Controllers\ClienteController::class);
    Route::resource('/user',\App\Http\Controllers\UserController::class);
    Route::put('/pass/{user}',[\App\Http\Controllers\UserController::class,'pass']);
    Route::put('/updatepermisos/{user}',[\App\Http\Controllers\UserController::class,'updatepermisos']);
    Route::resource('/permiso',\App\Http\Controllers\PermisoController::class);
    Route::post('/orden',[\App\Http\Controllers\PermisoController::class,'orden']);
    Route::resource('/producto',\App\Http\Controllers\ProductoController::class);
    Route::resource('/venta',\App\Http\Controllers\VentaController::class);
    Route::resource('/garantia',\App\Http\Controllers\GarantiaController::class);
    Route::resource('/inventario',\App\Http\Controllers\InventarioController::class);
    Route::resource('/loginventario',\App\Http\Controllers\LoginventarioController::class);
    Route::resource('/gasto',\App\Http\Controllers\GastoController::class);
    Route::resource('/prestamo',\App\Http\Controllers\PrestamoController::class);
    Route::resource('/planilla',\App\Http\Controllers\PlanillaController::class);
    Route::resource('/logplanilla',\App\Http\Controllers\LogplanillaController::class);
    Route::post('/misgastos',[\App\Http\Controllers\GastoController::class,'misgastos']);
    Route::post('/listglosa',[\App\Http\Controllers\GastoController::class,'listglosa']);
    Route::post('/gastocaja',[\App\Http\Controllers\GastoController::class,'gastocaja']);

    Route::resource('/user',\App\Http\Controllers\UserController::class);
    Route::put('/pass/{user}',[\App\Http\Controllers\UserController::class,'pass']);
    Route::put('/updatepermisos/{user}',[\App\Http\Controllers\UserController::class,'updatepermisos']);
    Route::resource('/permiso',\App\Http\Controllers\PermisoController::class);
    Route::resource('/pago',\App\Http\Controllers\PagoController::class);
    Route::post('/reportepago',[\App\Http\Controllers\PagoController::class,'reportepago']);
    Route::post('/repalmacen',[\App\Http\Controllers\MaterialController::class,'repalmacen']);

    Route::post('/listlog',[\App\Http\Controllers\LoginventarioController::class,'listlog']);
    Route::post('/misventas',[\App\Http\Controllers\VentaController::class,'misventas']);
    Route::post('/listventa',[\App\Http\Controllers\VentaController::class,'listventa']);

    Route::post('/listSale',[\App\Http\Controllers\SaleController::class,'listSale']);
    Route::get('/listInv',[\App\Http\Controllers\InventarioController::class,'listInv']);

    Route::post('/listventdetalle',[\App\Http\Controllers\VentaController::class,'listventdetalle']);
    Route::post('/listventlocal',[\App\Http\Controllers\VentaController::class,'listventlocal']);
    Route::post('/listventruta',[\App\Http\Controllers\VentaController::class,'listventruta']);
    Route::post('/directa',[\App\Http\Controllers\VentaController::class,'directa']);
    Route::post('/listado',[\App\Http\Controllers\GarantiaController::class,'listado']);
    Route::post('/activar',[\App\Http\Controllers\ClienteController::class,'activar']);
    Route::post('/activarprod',[\App\Http\Controllers\ProductoController::class,'activarprod']);
    Route::post('/activarinv',[\App\Http\Controllers\InventarioController::class,'activarinv']);
    Route::post('/inventarioadd',[\App\Http\Controllers\InventarioController ::class,'productadd']);
    Route::post('/inventariosub',[\App\Http\Controllers\InventarioController ::class,'productsub']);
    Route::get('/listacliente',[\App\Http\Controllers\ClienteController ::class,'listacliente']);
    Route::get('/listacliente2/{type}',[\App\Http\Controllers\ClienteController ::class,'listacliente2']);
    Route::get('/listaproducto',[\App\Http\Controllers\ProductoController ::class,'listaproducto']);
    Route::get('/listaproducto/{type}',[\App\Http\Controllers\ProductoController ::class,'listaproducto2']);
    Route::get('/listainventario',[\App\Http\Controllers\InventarioController ::class,'listainventario']);
    Route::post('/listaprestamo',[\App\Http\Controllers\GarantiaController ::class,'listaprestamo']);
    Route::post('/listadoventa',[\App\Http\Controllers\VentaController ::class,'listadoventa']);
    Route::post('/listadoventaruta',[\App\Http\Controllers\VentaController ::class,'listadoventaruta']);
    Route::post('/listadorep',[\App\Http\Controllers\VentaController ::class,'listado']);
    Route::post('/listadodeudores',[\App\Http\Controllers\VentaController ::class,'listadodeudores']);
    Route::post('/tefectivo',[\App\Http\Controllers\PrestamoController ::class,'tefectivo']);
    Route::post('/devolver',[\App\Http\Controllers\GarantiaController ::class,'devolver']);
    Route::get('/cumple',[\App\Http\Controllers\ClienteController ::class,'ordercumple']);
    Route::get('/cumple2',[\App\Http\Controllers\ClienteController ::class,'ordercumple2']);
    Route::get('/cumple3',[\App\Http\Controllers\ClienteController ::class,'cumple3']);
    Route::get('/aniver',[\App\Http\Controllers\ClienteController ::class,'aniver']);
    Route::post('/impresiondetalle/{id}',[\App\Http\Controllers\VentaController ::class,'impresiondetalle']);
    Route::post('/anular',[\App\Http\Controllers\VentaController ::class,'anular']);
    Route::post('/ruta/{id}',[\App\Http\Controllers\VentaController ::class,'ruta']);
    Route::resource('/logprestamo',\App\Http\Controllers\LogprestamoController ::class);

    Route::resource('/empleado',\App\Http\Controllers\EmpleadoController::class);
    Route::resource('/compra',\App\Http\Controllers\CompraController::class);
    Route::post('/compra2',[\App\Http\Controllers\CompraController::class,'compra2']);
    Route::post('/missueldos',[\App\Http\Controllers\EmpleadoController::class,'missueldos']);
    Route::resource('/sueldo',\App\Http\Controllers\SueldoController::class);
    Route::get('/impade/{id}',[\App\Http\Controllers\SueldoController::class,'impade']);
    Route::post('/cancelacion',[\App\Http\Controllers\SueldoController::class,'cancelacion']);
    Route::post('/agregarcliente',[\App\Http\Controllers\ClienteController::class,'agregarcliente']);
    Route::post('/reportecliente',[\App\Http\Controllers\PrestamoController::class,'reportecliente']);
    Route::post('/anularprestamo',[\App\Http\Controllers\PrestamoController::class,'anularprestamo']);
    Route::post('/misanulados',[\App\Http\Controllers\PrestamoController::class,'misanulados']);
    Route::post('/modprestamo',[\App\Http\Controllers\PrestamoController::class,'modprestamo']);
    Route::post('/reporteventa',[\App\Http\Controllers\PrestamoController::class,'reporteventa']);
    Route::resource('/logcaja',\App\Http\Controllers\LogcajaController::class);
    Route::resource('/provider',\App\Http\Controllers\ProviderController::class);
    Route::resource('/material',\App\Http\Controllers\MaterialController::class);
    Route::resource('/recuento',\App\Http\Controllers\RecuentoController::class);
    Route::resource('/glosa',\App\Http\Controllers\GlosaController::class);
    Route::resource('/general',\App\Http\Controllers\GeneralController::class);
    Route::resource('/loggeneral',\App\Http\Controllers\LoggeneralController::class);
    Route::resource('/logcompra',\App\Http\Controllers\LogcompraController::class);
    Route::post('/listcaja',[\App\Http\Controllers\LogcajaController::class,'listcaja']);
    Route::post('/listgeneral',[\App\Http\Controllers\LoggeneralController::class,'listgeneral']);
    Route::post('/totalcaja',[\App\Http\Controllers\LogcajaController::class,'totalcaja']);
    Route::post('/totalgeneral',[\App\Http\Controllers\LoggeneralController::class,'totalgeneral']);
    Route::post('/repventa',[\App\Http\Controllers\ContableController::class,'repventa']);
    Route::post('/repventpago',[\App\Http\Controllers\ContableController::class,'repventpago']);
    Route::post('/repingprestamo',[\App\Http\Controllers\ContableController::class,'repingprestamo']);
    Route::post('/repgastos',[\App\Http\Controllers\ContableController::class,'repgastos']);
    Route::post('/repcompra',[\App\Http\Controllers\ContableController::class,'repcompra']);
    Route::post('/repcaja',[\App\Http\Controllers\ContableController::class,'repcaja']);
    Route::post('/repcaja2',[\App\Http\Controllers\ContableController::class,'repcaja2']);
    Route::post('/consultar',[\App\Http\Controllers\CompraController::class,'consultar']);
    Route::post('/consultar2',[\App\Http\Controllers\CompraController::class,'consultar2']);
//    observacionAdmin
    Route::post('/observacionAdmin',[\App\Http\Controllers\CompraController::class,'observacionAdmin']);

    Route::post('/valplanilla',[\App\Http\Controllers\PlanillaController::class,'valplanilla']);
    Route::post('/replanilla',[\App\Http\Controllers\PlanillaController::class,'replanilla']);
    Route::post('/consulrecuento',[\App\Http\Controllers\RecuentoController::class,'consulrecuento']);
    Route::post('/consulrecuento2',[\App\Http\Controllers\RecuentoController::class,'consulrecuento2']);
    Route::post('/cambioestado',[\App\Http\Controllers\UserController::class,'cambioestado']);
    Route::post('/estadoEmpleado',[\App\Http\Controllers\EmpleadoController::class,'estadoEmpleado']);
    Route::post('/impresionVenta/{id}',[\App\Http\Controllers\SaleController::class,'impresionVenta']);
    Route::post('/updateRuta',[\App\Http\Controllers\SaleController::class,'updateRuta']);
    Route::post('/impresionruta/{id}',[\App\Http\Controllers\SaleController::class,'impresionruta']);
    Route::post('/reportSale',[\App\Http\Controllers\SaleController::class,'reportSale']);
    Route::get('/listEmpleado',[\App\Http\Controllers\EmpleadoController::class,'listEmpleado']);
    Route::post('/compraModificar',[\App\Http\Controllers\CompraController::class,'compraModificar']);


    Route::resource('/sale',\App\Http\Controllers\SaleController::class);
    Route::post('/impdevolucion/{id}',[\App\Http\Controllers\LogprestamoController::class,'impdevolucion']);
    Route::post('/impresion/{id}',[\App\Http\Controllers\PrestamoController::class,'impresion']);
    Route::post('/impresionRep/{id}',[\App\Http\Controllers\PrestamoController::class,'impresionRep']);

    Route::post('/anularLogcompra',[\App\Http\Controllers\LogcompraController::class,'anularLogcompra']);
    Route::get('/impPagoAlmacen/{id}',[\App\Http\Controllers\LogcompraController::class,'impPagoAlmacen']);
    Route::post('/repClienteVenta',[\App\Http\Controllers\SaleController::class,'repClienteVenta']);
    Route::post('/repClienteVenta2',[\App\Http\Controllers\SaleController::class,'repClienteVenta2']);

    Route::post('/listPag',[\App\Http\Controllers\PrestamoController::class,'listPag']);
    Route::post('/regprestamo',[\App\Http\Controllers\PrestamoController::class,'regprestamo']);

    Route::post('/loginHistorial',[\App\Http\Controllers\UserController::class,'loginHistorial']);

    Route::post('/totalCajaIng',[\App\Http\Controllers\ContableController::class,'totalCajaIng']);

    Route::get('/empleadoSueldo',[\App\Http\Controllers\EmpleadoController::class,'empleadoSueldo']);

    Route::post('/totalReporte',[\App\Http\Controllers\LogcompraController::class,'totalReporte']); //informeCaja
});
Route::get('/cuentasCobrarDetalle',[\App\Http\Controllers\ReporteController::class,'cuentasCobrarDetalle']);
Route::get('/cuentasCobrarLocal',[\App\Http\Controllers\ReporteController::class,'cuentasCobrarLocal']);
