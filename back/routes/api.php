<?php

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\GraderiaController;
use App\Http\Controllers\AsientoController;

Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::get('/public/graderias/{code}', [GraderiaController::class, 'publicShowByCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/me/password', [App\Http\Controllers\UserController::class, 'changeMyPassword']);

    // Admin resetea contraseña de otro usuario
    Route::post('/users/{user}/password-reset', [App\Http\Controllers\UserController::class, 'adminResetPassword']);

    Route::get('/me', [App\Http\Controllers\UserController::class, 'me']);
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);

    Route::get('/users', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store']);
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy']);

    Route::put('/updatePassword/{user}', [App\Http\Controllers\UserController::class, 'updatePassword']);
    Route::post('/{user}/avatar', [App\Http\Controllers\UserController::class, 'updateAvatar']);

    Route::get('/permissions', [App\Http\Controllers\UserController::class, 'permissions']);
    Route::get('/users/{user}/permissions', [App\Http\Controllers\UserController::class, 'userPermissions']);
    Route::put('/users/{user}/permissions', [App\Http\Controllers\UserController::class, 'updateUserPermissions']);

    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{cliente}/historial', [ClienteController::class, 'historial']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy']);
    Route::get('/clientes/pdf', [ClienteController::class, 'pdf']);

    Route::get('/productos', [ProductoController::class, 'index']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);

    Route::get('/personales', [PersonalController::class, 'index']);
    Route::post('/personales', [PersonalController::class, 'store']);
    Route::put('/personales/{personal}', [PersonalController::class, 'update']);
    Route::delete('/personales/{personal}', [PersonalController::class, 'destroy']);
    Route::post('/personales/{personal}', [PersonalController::class, 'update']);

    Route::get('/inventarios', [InventarioController::class, 'index']);
    Route::post('/inventarios', [InventarioController::class, 'store']);
    Route::put('/inventarios/{inventario}', [InventarioController::class, 'update']);
    Route::delete('/inventarios/{inventario}', [InventarioController::class, 'destroy']);

    Route::get('/prestamos', [PrestamoController::class, 'index']);
    Route::post('/prestamos', [PrestamoController::class, 'store']);
    Route::post('/prestamos/{prestamo}/retornar', [PrestamoController::class, 'retornar']);

    Route::get('/cajas', [CajaController::class, 'index']);

    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas/deudas/detalle', [VentaController::class, 'deudasDetalle']);
    Route::get('/ventas/deudas', [VentaController::class, 'deudasDetalle']);
    Route::get('/ventas/{venta}', [VentaController::class, 'show']);
    Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf']);
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::put('/ventas/{venta}', [VentaController::class, 'update']);
    Route::post('/ventas/{venta}/anular', [VentaController::class, 'anular']);
    Route::post('/ventas/{venta}/amortizar', [VentaController::class, 'amortizar']);
    Route::post('/ventas/{venta}/ocultar-deuda', [VentaController::class, 'ocultarDeuda']);
    Route::post('/ventas/{venta}/pagos/{pago}/anular', [VentaController::class, 'anularPago']);
    Route::post('/ventas/{venta}/pagos/{pago}/pagar', [VentaController::class, 'pagarCuota']);

});
