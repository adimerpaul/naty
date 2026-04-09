<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\Impuestos\SiatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImpuestoController extends Controller
{
    public function __construct(private readonly SiatService $siatService)
    {
    }

    public function estado(): JsonResponse
    {
        return response()->json($this->siatService->estadoActual());
    }

    public function generarCuis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_punto_venta' => 'nullable|integer|min:0',
            'codigo_sucursal' => 'nullable|integer|min:0',
        ]);

        try {
            $cuis = $this->siatService->generarCuis(
                $validated['codigo_punto_venta'] ?? config('siat.codigo_punto_venta'),
                $validated['codigo_sucursal'] ?? config('siat.codigo_sucursal'),
            );

            return response()->json([
                'message' => 'CUIS generado correctamente.',
                'cuis' => $cuis,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function generarCufd(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_punto_venta' => 'nullable|integer|min:0',
            'codigo_sucursal' => 'nullable|integer|min:0',
        ]);

        try {
            $cufd = $this->siatService->generarCufd(
                $validated['codigo_punto_venta'] ?? config('siat.codigo_punto_venta'),
                $validated['codigo_sucursal'] ?? config('siat.codigo_sucursal'),
            );

            return response()->json([
                'message' => 'CUFD generado correctamente.',
                'cufd' => $cufd,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function facturarVenta(Venta $venta): JsonResponse
    {
        try {
            $resultado = $this->siatService->facturarVenta($venta);
            $venta->refresh()->load(['detalles', 'pagos', 'user', 'caja', 'prestamos.inventario', 'cliente']);

            return response()->json([
                'message' => $resultado['ok'] ? 'Factura enviada a SIAT.' : 'La factura fue observada por SIAT.',
                'facturacion' => $resultado,
                'venta' => $venta,
            ]);
        } catch (\Throwable $e) {
            Venta::query()->whereKey($venta->id)->update([
                'factura_estado' => 'ERROR',
                'factura_error' => $e->getMessage(),
            ]);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
