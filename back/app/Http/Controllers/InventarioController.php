<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\InventarioMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventario::query()->orderBy('orden')->orderBy('id', 'desc');
        if ($request->filled('search')) {
            $s = trim((string)$request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                    ->orWhere('codigo', 'like', "%{$s}%")
                    ->orWhere('detalle', 'like', "%{$s}%");
            });
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'nombre' => 'required|string|max:255',
            'cantidad' => 'nullable|integer|min:0',
            'detalle' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'estado' => 'nullable|string|max:30',
            'precio' => 'nullable|numeric|min:0',
        ]);
        if (! isset($validated['cantidad'])) {
            $validated['cantidad'] = 0;
        }

        return Inventario::create($validated);
    }

    public function update(Request $request, Inventario $inventario)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'nombre' => 'sometimes|required|string|max:255',
            'detalle' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'estado' => 'nullable|string|max:30',
            'precio' => 'nullable|numeric|min:0',
        ]);
        $inventario->update($validated);
        return $inventario;
    }

    public function movimientos(Request $request, Inventario $inventario)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = InventarioMovimiento::with(['user', 'anuladoPor'])
            ->where('inventario_id', $inventario->id)
            ->orderByDesc('id');

        if (! empty($validated['date_from'])) {
            $query->whereDate('fecha', '>=', $validated['date_from']);
        }
        if (! empty($validated['date_to'])) {
            $query->whereDate('fecha', '<=', $validated['date_to']);
        }

        return $query->get();
    }

    public function registrarMovimiento(Request $request, Inventario $inventario)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'tipo' => ['required', Rule::in(['AUMENTO', 'DISMINUCION'])],
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $inventario, $validated) {
            /** @var Inventario $inventarioLocked */
            $inventarioLocked = Inventario::lockForUpdate()->findOrFail($inventario->id);
            $anterior = (int) $inventarioLocked->cantidad;
            $cantidad = (int) $validated['cantidad'];
            $tipo = $validated['tipo'];

            if ($tipo === 'DISMINUCION' && $cantidad > $anterior) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La disminucion supera el inventario disponible.',
                ]);
            }

            $nueva = $tipo === 'AUMENTO' ? $anterior + $cantidad : $anterior - $cantidad;
            $inventarioLocked->update(['cantidad' => $nueva]);

            return InventarioMovimiento::create([
                'inventario_id' => $inventarioLocked->id,
                'user_id' => $request->user()->id ?? null,
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'tipo' => $tipo,
                'estado' => 'REGISTRADO',
                'cantidad' => $cantidad,
                'cantidad_anterior' => $anterior,
                'cantidad_nueva' => $nueva,
                'motivo' => $validated['motivo'] ?? null,
            ]);
        });
    }

    public function anularMovimiento(Request $request, Inventario $inventario, InventarioMovimiento $movimiento)
    {
        $validated = $request->validate([
            'motivo_anulacion' => 'nullable|string|max:255',
        ]);

        if ((int) $movimiento->inventario_id !== (int) $inventario->id) {
            return response()->json(['message' => 'El movimiento no corresponde al inventario seleccionado.'], 422);
        }
        if ($movimiento->estado === 'ANULADO') {
            return response()->json(['message' => 'El movimiento ya se encuentra anulado.'], 422);
        }

        return DB::transaction(function () use ($request, $inventario, $movimiento, $validated) {
            /** @var Inventario $inventarioLocked */
            $inventarioLocked = Inventario::lockForUpdate()->findOrFail($inventario->id);
            /** @var InventarioMovimiento $movimientoLocked */
            $movimientoLocked = InventarioMovimiento::lockForUpdate()->findOrFail($movimiento->id);

            $actual = (int) $inventarioLocked->cantidad;
            $cantidadMovimiento = (int) $movimientoLocked->cantidad;

            if ($movimientoLocked->tipo === 'AUMENTO') {
                if ($cantidadMovimiento > $actual) {
                    return response()->json([
                        'message' => 'No se puede anular el aumento porque el stock actual es menor al movimiento.',
                    ], 422);
                }
                $nuevaCantidad = $actual - $cantidadMovimiento;
            } else {
                $nuevaCantidad = $actual + $cantidadMovimiento;
            }

            $inventarioLocked->update(['cantidad' => $nuevaCantidad]);
            $movimientoLocked->update([
                'estado' => 'ANULADO',
                'anulado_at' => now(),
                'anulado_por' => $request->user()->id ?? null,
                'motivo_anulacion' => $validated['motivo_anulacion'] ?? null,
            ]);

            return $movimientoLocked->fresh(['user', 'anuladoPor']);
        });
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return response()->json(['message' => 'Inventario eliminado']);
    }
}
