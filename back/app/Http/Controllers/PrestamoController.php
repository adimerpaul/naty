<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestamo::with(['cliente', 'inventario', 'venta'])->orderBy('id', 'desc');
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'tipo' => ['required', Rule::in(['prestamo', 'venta'])],
            'cliente_id' => 'required|exists:clientes,id',
            'inventario_id' => 'required|exists:inventarios,id',
            'venta_id' => 'nullable|exists:ventas,id',
            'cantidad' => 'required|integer|min:1',
            'efectivo' => 'nullable|numeric|min:0',
            'fisico' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:255',
            'tipo_venta' => ['nullable', Rule::in(['detalle', 'local'])],
            'metodo_pago' => ['nullable', Rule::in(['efectivo', 'qr'])],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $inv = Inventario::lockForUpdate()->findOrFail($validated['inventario_id']);
            if ($inv->cantidad < (int) $validated['cantidad']) {
                return response()->json(['message' => 'No hay cantidad suficiente en inventario'], 422);
            }

            $inv->update(['cantidad' => $inv->cantidad - (int) $validated['cantidad']]);

            $ventaGenerada = null;
            $cliente = Cliente::find($validated['cliente_id']);
            if ($validated['tipo'] === 'venta') {
                $monto = (float) ($validated['efectivo'] ?? 0);
                if ($monto <= 0) {
                    return response()->json(['message' => 'Debe registrar efectivo para venta de material'], 422);
                }
                $ventaGenerada = Venta::create([
                    'caja_id' => 1,
                    'cliente_id' => $cliente?->id,
                    'user_id' => $request->user()->id ?? null,
                    'tipo_venta' => $validated['tipo_venta'] ?? 'detalle',
                    'tipo_movimiento' => 'ingreso',
                    'tipo_pago' => 'contado',
                    'estado' => 'ACTIVA',
                    'cliente_nombre' => $cliente?->nombre,
                    'cliente_telefono' => $cliente?->telefono,
                    'cliente_direccion' => $cliente?->direccion,
                    'observacion' => 'Venta de material inventario. ' . ($validated['observacion'] ?? ''),
                    'total' => round($monto, 2),
                ]);
                VentaDetalle::create([
                    'venta_id' => $ventaGenerada->id,
                    'producto_id' => null,
                    'user_id' => $request->user()->id ?? null,
                    'producto_nombre' => "Material: {$inv->nombre}",
                    'precio' => round($monto / max((int)$validated['cantidad'], 1), 2),
                    'cantidad' => (int)$validated['cantidad'],
                    'subtotal' => round($monto, 2),
                    'estado' => true,
                ]);
                Pago::create([
                    'venta_id' => $ventaGenerada->id,
                    'user_id' => $request->user()->id ?? null,
                    'nro_cuota' => 1,
                    'monto' => round($monto, 2),
                    'fecha_programada' => now()->toDateString(),
                    'fecha_pago' => now(),
                    'metodo' => $validated['metodo_pago'] ?? 'efectivo',
                    'estado' => 'PAGADO',
                ]);
            }

            $prestamo = Prestamo::create([
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'tipo' => $validated['tipo'],
                'estado' => $validated['tipo'] === 'venta' ? 'VENDIDO' : 'EN PRESTAMO',
                'efectivo' => $validated['efectivo'] ?? 0,
                'fisico' => $validated['fisico'] ?? '',
                'observacion' => $validated['observacion'] ?? '',
                'cantidad' => (int) $validated['cantidad'],
                'prestado' => $validated['tipo'] === 'venta',
                'user_id' => $request->user()->id ?? null,
                'cliente_id' => $validated['cliente_id'],
                'inventario_id' => $validated['inventario_id'],
                'venta_id' => $validated['venta_id'] ?? $ventaGenerada?->id,
            ]);

            return $prestamo->load(['cliente', 'inventario', 'venta']);
        });
    }

    public function retornar(Request $request, Prestamo $prestamo)
    {
        if ($prestamo->estado !== 'EN PRESTAMO') {
            return response()->json(['message' => 'Solo se puede retornar prestamos en estado EN PRESTAMO'], 422);
        }

        return DB::transaction(function () use ($prestamo) {
            $inventario = Inventario::lockForUpdate()->findOrFail($prestamo->inventario_id);
            $inventario->update([
                'cantidad' => (int) $inventario->cantidad + (int) $prestamo->cantidad,
            ]);

            $prestamo->update([
                'estado' => 'RETORNADO',
                'prestado' => false,
                'observacion' => trim(($prestamo->observacion ? $prestamo->observacion . ' | ' : '') . 'Retornado'),
            ]);

            return $prestamo->load(['cliente', 'inventario', 'venta']);
        });
    }
}
