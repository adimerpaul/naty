<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\LogCompra;
use App\Models\Material;
use App\Models\Pago;
use App\Models\Provider;
use App\Models\Recuento;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function materialsIndex()
    {
        return Material::query()->orderBy('nombre')->get();
    }

    public function materialsStore(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad' => 'required|string|max:50',
            'minimo' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
        ]);

        return Material::create([
            'nombre' => trim($validated['nombre']),
            'unidad' => trim($validated['unidad']),
            'minimo' => $this->amount($validated['minimo'] ?? 0),
            'stock' => $this->amount($validated['stock'] ?? 0),
        ]);
    }

    public function materialsUpdate(Request $request, Material $material)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'unidad' => 'sometimes|required|string|max:50',
            'minimo' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
        ]);

        if (array_key_exists('nombre', $validated)) {
            $validated['nombre'] = trim($validated['nombre']);
        }
        if (array_key_exists('unidad', $validated)) {
            $validated['unidad'] = trim($validated['unidad']);
        }
        if (array_key_exists('minimo', $validated)) {
            $validated['minimo'] = $this->amount($validated['minimo']);
        }
        if (array_key_exists('stock', $validated)) {
            $validated['stock'] = $this->amount($validated['stock']);
        }

        $material->update($validated);

        return $material->fresh();
    }

    public function providersIndex()
    {
        return Provider::query()->orderByDesc('estado')->orderBy('razon')->get();
    }

    public function providersStore(Request $request)
    {
        $validated = $request->validate([
            'razon' => 'required|string|max:255',
            'nombre' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'estado' => 'nullable|boolean',
        ]);

        return Provider::create([
            'razon' => trim($validated['razon']),
            'nombre' => $this->nullableTrim($validated['nombre'] ?? null),
            'nit' => $this->nullableTrim($validated['nit'] ?? null),
            'direccion' => $this->nullableTrim($validated['direccion'] ?? null),
            'telefono' => $this->nullableTrim($validated['telefono'] ?? null),
            'estado' => (bool) ($validated['estado'] ?? true),
        ]);
    }

    public function providersUpdate(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'razon' => 'sometimes|required|string|max:255',
            'nombre' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'estado' => 'nullable|boolean',
        ]);

        foreach (['razon', 'nombre', 'nit', 'direccion', 'telefono'] as $field) {
            if (array_key_exists($field, $validated)) {
                $validated[$field] = $field === 'razon'
                    ? trim($validated[$field])
                    : $this->nullableTrim($validated[$field]);
            }
        }

        $provider->update($validated);

        return $provider->fresh();
    }

    public function comprasIndex()
    {
        return Compra::with(['material', 'provider', 'user', 'pagos', 'recuentos'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->orderByDesc('id')
            ->get();
    }

    public function comprasStore(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'hora' => 'nullable|date_format:H:i',
            'cantidad' => 'required|numeric|min:0.01',
            'costo' => 'required|numeric|min:0',
            'fechaven' => 'nullable|date',
            'lote' => 'nullable|string|max:255',
            'comentario' => 'nullable|string',
            'observacion' => 'nullable|string',
            'material_id' => 'required|exists:materials,id',
            'provider_id' => 'required|exists:providers,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $material = Material::lockForUpdate()->findOrFail($validated['material_id']);
            $cantidad = $this->amount($validated['cantidad']);
            $costo = $this->amount($validated['costo']);
            $subtotal = $this->amount($cantidad * $costo);

            $compra = Compra::create([
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'hora' => $validated['hora'] ?? now()->format('H:i'),
                'cantidad' => $cantidad,
                'retiro' => 0,
                'costo' => $costo,
                'subtotal' => $subtotal,
                'deuda' => $subtotal,
                'fechaven' => $validated['fechaven'] ?? null,
                'lote' => $this->nullableTrim($validated['lote'] ?? null),
                'comentario' => $this->nullableTrim($validated['comentario'] ?? null),
                'observacion' => $this->nullableTrim($validated['observacion'] ?? null),
                'estado' => 'PENDIENTE',
                'material_id' => $material->id,
                'provider_id' => $validated['provider_id'],
                'user_id' => $request->user()->id ?? null,
            ]);

            $material->update([
                'stock' => $this->amount($material->stock + $cantidad),
            ]);

            return $compra->load(['material', 'provider', 'user']);
        });
    }

    public function pagosIndex()
    {
        return LogCompra::with(['compra.material', 'compra.provider', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();
    }

    public function pagosStore(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
            'compra_id' => 'required|exists:compras,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $compra = Compra::with('material')->lockForUpdate()->findOrFail($validated['compra_id']);
            $monto = $this->amount($validated['monto']);

            if ($compra->deuda <= 0) {
                return response()->json(['message' => 'La compra ya no tiene deuda pendiente'], 422);
            }
            if ($monto > $compra->deuda) {
                return response()->json(['message' => 'El pago supera la deuda pendiente'], 422);
            }

            $cajaGeneral = Caja::query()
                ->where('estado', true)
                ->where('nombre', 'Caja General')
                ->first();

            if (!$cajaGeneral) {
                return response()->json(['message' => 'No existe Caja General activa'], 422);
            }

            $saldoAntes = $this->saldoCaja($cajaGeneral->id);
            if ($saldoAntes < $monto) {
                return response()->json(['message' => 'Saldo insuficiente en Caja General'], 422);
            }

            $obs = $this->nullableTrim($validated['observacion'] ?? null);
            $venta = $this->crearEgresoCajaGeneral($cajaGeneral, $monto, $request->user()->id ?? null, $compra, $obs);
            $saldoDespues = $this->saldoCaja($cajaGeneral->id);

            $pago = LogCompra::create([
                'fecha' => $validated['fecha'] ?? now()->toDateString(),
                'monto' => $monto,
                'caja' => $saldoDespues,
                'observacion' => $obs ?: "Pago de compra #{$compra->id}",
                'compra_id' => $compra->id,
                'user_id' => $request->user()->id ?? null,
            ]);

            $compra->deuda = $this->amount(max(0, $compra->deuda - $monto));
            $compra->estado = $this->resolveCompraEstado($compra);
            $compra->save();

            return response()->json([
                'message' => 'Pago registrado',
                'pago' => $pago->load(['compra.material', 'compra.provider', 'user']),
                'movimiento_caja_id' => $venta->id,
            ]);
        });
    }

    public function recuentosIndex()
    {
        return Recuento::with(['material', 'compra.material', 'compra.provider', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->orderByDesc('id')
            ->get();
    }

    public function recuentosStore(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'nullable|date',
            'hora' => 'nullable|date_format:H:i',
            'cantidad' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
            'material_id' => 'required|exists:materials,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $material = Material::lockForUpdate()->findOrFail($validated['material_id']);
            $cantidadSolicitada = $this->amount($validated['cantidad']);

            if ($material->stock < $cantidadSolicitada) {
                return response()->json(['message' => 'Stock insuficiente para este retiro'], 422);
            }

            $compras = Compra::query()
                ->where('material_id', $material->id)
                ->whereRaw('cantidad > retiro')
                ->orderBy('fecha')
                ->orderBy('hora')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $disponible = $this->amount($compras->sum(fn ($compra) => $compra->cantidad - $compra->retiro));
            if ($disponible < $cantidadSolicitada) {
                return response()->json(['message' => 'No existen compras suficientes para aplicar el retiro FIFO'], 422);
            }

            $fecha = $validated['fecha'] ?? now()->toDateString();
            $hora = $validated['hora'] ?? now()->format('H:i');
            $observacion = $this->nullableTrim($validated['observacion'] ?? null);
            $restante = $cantidadSolicitada;
            $rows = collect();

            foreach ($compras as $compra) {
                if ($restante <= 0) {
                    break;
                }

                $pendienteCompra = $this->amount($compra->cantidad - $compra->retiro);
                if ($pendienteCompra <= 0) {
                    continue;
                }

                $consume = $this->amount(min($restante, $pendienteCompra));

                $recuento = Recuento::create([
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'cantidad' => $consume,
                    'observacion' => $observacion,
                    'material_id' => $material->id,
                    'compra_id' => $compra->id,
                    'user_id' => $request->user()->id ?? null,
                ]);

                $compra->retiro = $this->amount($compra->retiro + $consume);
                $compra->estado = $this->resolveCompraEstado($compra);
                $compra->save();

                $rows->push($recuento);
                $restante = $this->amount($restante - $consume);
            }

            $material->stock = $this->amount($material->stock - $cantidadSolicitada);
            $material->save();

            return response()->json([
                'message' => 'Retiro registrado',
                'items' => Recuento::with(['material', 'compra.material', 'compra.provider', 'user'])
                    ->whereIn('id', $rows->pluck('id'))
                    ->get(),
            ]);
        });
    }

    private function crearEgresoCajaGeneral(Caja $caja, float $monto, ?int $userId, Compra $compra, ?string $observacion): Venta
    {
        $venta = Venta::create([
            'caja_id' => $caja->id,
            'cliente_id' => null,
            'user_id' => $userId,
            'tipo_venta' => 'caja',
            'tipo_movimiento' => 'egreso',
            'tipo_pago' => 'contado',
            'estado' => 'ACTIVA',
            'cliente_nombre' => null,
            'cliente_telefono' => null,
            'cliente_direccion' => null,
            'total' => $monto,
            'observacion' => $observacion ?: "Pago compra almacen #{$compra->id} - {$compra->material->nombre}",
        ]);

        Pago::create([
            'venta_id' => $venta->id,
            'user_id' => $userId,
            'nro_cuota' => 1,
            'monto' => $monto,
            'fecha_programada' => now()->toDateString(),
            'fecha_pago' => now(),
            'metodo' => 'efectivo',
            'estado' => 'PAGADO',
            'observacion' => "Pago de compra #{$compra->id}",
        ]);

        return $venta;
    }

    private function resolveCompraEstado(Compra $compra): string
    {
        $deuda = $this->amount($compra->deuda);
        $pendienteRetiro = $this->amount($compra->cantidad - $compra->retiro);

        if ($deuda <= 0 && $pendienteRetiro <= 0) {
            return 'PAGADA / CONSUMIDA';
        }
        if ($deuda <= 0) {
            return 'PAGADA';
        }
        if ($pendienteRetiro <= 0) {
            return 'CONSUMIDA';
        }
        if ($compra->retiro > 0) {
            return 'PARCIAL';
        }

        return 'PENDIENTE';
    }

    private function saldoCaja(int $cajaId): float
    {
        $pagos = DB::table('pagos')
            ->selectRaw('venta_id, SUM(monto) as pagado')
            ->where('estado', 'PAGADO')
            ->groupBy('venta_id');

        $row = DB::table('ventas as v')
            ->leftJoinSub($pagos, 'pg', function ($join) {
                $join->on('pg.venta_id', '=', 'v.id');
            })
            ->whereNull('v.deleted_at')
            ->where('v.caja_id', $cajaId)
            ->where('v.estado', 'ACTIVA')
            ->selectRaw("
                SUM(
                    CASE WHEN v.tipo_movimiento='ingreso'
                        THEN (CASE WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0) ELSE COALESCE(pg.pagado, v.total) END)
                        ELSE 0 END
                ) as ingresos,
                SUM(
                    CASE WHEN v.tipo_movimiento='egreso'
                        THEN (CASE WHEN v.tipo_pago='credito' THEN COALESCE(pg.pagado,0) ELSE COALESCE(pg.pagado, v.total) END)
                        ELSE 0 END
                ) as egresos
            ")
            ->first();

        return $this->amount(((float) ($row->ingresos ?? 0)) - ((float) ($row->egresos ?? 0)));
    }

    private function amount($value): float
    {
        return round((float) $value, 2);
    }

    private function nullableTrim($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
