<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\PersonalPago;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['caja', 'detalles', 'pagos', 'user'])->orderBy('id', 'desc');

        if ($request->filled('tipo_venta')) {
            $query->where('tipo_venta', $request->tipo_venta);
        }
        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }
        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', $request->tipo_pago);
        }
        if ($request->filled('metodo_pago')) {
            $query->whereHas('pagos', function ($q) use ($request) {
                $q->where('metodo', $request->metodo_pago);
            });
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('cliente_nombre', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $ventas = $query->get();
        return $ventas->map(fn (Venta $v) => $this->withResumen($v));
    }

    public function show(Venta $venta)
    {
        $venta->load(['caja', 'detalles', 'pagos', 'user', 'prestamos.inventario']);
        return $this->withResumen($venta);
    }

    public function pdf(Venta $venta)
    {
        $venta->load(['detalles', 'pagos', 'user']);
        $pdf = Pdf::loadView('pdf.venta', [
            'venta' => $venta,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('venta-' . $venta->id . '.pdf');
    }

    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'tipo_movimiento' => ['required', Rule::in(['ingreso', 'egreso'])],
            'tipo_pago' => ['required', Rule::in(['contado', 'credito'])],
            'observacion' => 'nullable|string|max:2000',
        ]);

        $venta->update([
            'tipo_movimiento' => $validated['tipo_movimiento'],
            'tipo_pago' => $validated['tipo_pago'],
            'observacion' => $validated['observacion'] ?? null,
        ]);

        $venta->load(['caja', 'detalles', 'pagos', 'user']);
        return $this->withResumen($venta);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'caja_id' => 'nullable|exists:cajas,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_venta' => ['required', Rule::in(['detalle', 'local'])],
            'tipo_movimiento' => ['nullable', Rule::in(['ingreso', 'egreso'])],
            'tipo_pago' => ['required', Rule::in(['contado', 'credito'])],
            'pago_inicial' => 'nullable|numeric|min:0',
            'metodo_pago' => ['nullable', Rule::in(['efectivo', 'qr'])],
            'concepto' => 'nullable|string|max:255',
            'monto' => 'nullable|integer|min:1',
            'observacion' => 'nullable|string|max:2000',
            'items' => 'nullable|array',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.producto_nombre' => 'nullable|string|max:255',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $cliente = null;
            if (!empty($validated['cliente_id'])) {
                $cliente = Cliente::find($validated['cliente_id']);
            }

            $venta = Venta::create([
                'caja_id' => $validated['caja_id'] ?? 1, // Caja General por defecto
                'cliente_id' => $cliente?->id,
                'user_id' => $request->user()->id ?? null,
                'tipo_venta' => $validated['tipo_venta'],
                'tipo_movimiento' => $validated['tipo_movimiento'] ?? 'ingreso',
                'tipo_pago' => $validated['tipo_pago'],
                'estado' => 'ACTIVA',
                'cliente_nombre' => $cliente?->nombre,
                'cliente_telefono' => $cliente?->telefono,
                'cliente_direccion' => $cliente?->direccion,
                'observacion' => $this->buildObservacion($validated),
                'total' => 0,
            ]);

            $items = $validated['items'] ?? [];
            $total = 0.0;
            if (!empty($items)) {
                foreach ($items as $item) {
                    $producto = null;
                    if (!empty($item['producto_id'])) {
                        $producto = Producto::find($item['producto_id']);
                    }
                    $cantidad = (float) $item['cantidad'];
                    $precio = (float) $item['precio'];
                    $subtotal = round($precio * $cantidad, 2);
                    $total += $subtotal;
                    $nombre = $producto?->nombre ?: trim((string) ($item['producto_nombre'] ?? 'Concepto manual'));
                    if ($nombre === '') {
                        $nombre = 'Concepto manual';
                    }

                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto?->id,
                        'user_id' => $request->user()->id ?? null,
                        'producto_nombre' => $nombre,
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'subtotal' => $subtotal,
                        'estado' => true,
                    ]);
                }
            } else {
                $total = round((float) ($validated['monto'] ?? 0), 2);
                if ($total <= 0) {
                    throw ValidationException::withMessages([
                        'monto' => 'Debe enviar items o un monto para ingreso/egreso',
                    ]);
                }
            }

            $venta->update(['total' => round($total, 2)]);

            if ($validated['tipo_pago'] === 'contado') {
                Pago::create([
                    'venta_id' => $venta->id,
                    'user_id' => $request->user()->id ?? null,
                    'nro_cuota' => 1,
                    'monto' => round($total, 2),
                    'fecha_programada' => now()->toDateString(),
                    'fecha_pago' => now(),
                    'metodo' => $validated['metodo_pago'] ?? 'efectivo',
                    'estado' => 'PAGADO',
                ]);
            } else {
                $this->crearPlanCuotas($venta, $request, $validated, round($total, 2));
            }

            $venta->load(['caja', 'detalles', 'pagos', 'user']);
            return $this->withResumen($venta);
        });
    }

    public function deudasDetalle(Request $request)
    {
        $tipoVenta = $request->tipo_venta ?: 'detalle';
        $query = Venta::with(['pagos', 'user'])
            ->withSum(['pagos as saldo_pendiente_sql' => fn ($q) => $q->where('estado', 'PENDIENTE')], 'monto')
            ->where('tipo_venta', $tipoVenta)
            ->where('tipo_pago', 'credito')
            ->where('estado', '!=', 'ANULADA')
            ->where('deuda_oculta', false)
            ->whereHas('pagos', fn ($q) => $q->where('estado', 'PENDIENTE'))
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('cliente_nombre', 'like', "%{$search}%")
                    ->orWhere('cliente_telefono', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $sort = strtolower((string) $request->get('sort_deuda', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->reorder('saldo_pendiente_sql', $sort)->orderBy('id', 'desc');
        $ventas = $query->get()->map(fn (Venta $v) => $this->withResumen($v));
        return $ventas->values();
    }

    public function amortizar(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo' => ['nullable', Rule::in(['efectivo', 'qr'])],
            'observacion' => 'nullable|string|max:255',
        ]);

        if ($venta->estado === 'ANULADA') {
            return response()->json(['message' => 'No se puede amortizar una venta anulada'], 422);
        }

        return DB::transaction(function () use ($validated, $venta, $request) {
            $pendientes = $venta->pagos()->where('estado', 'PENDIENTE')->orderBy('id')->get();
            $deuda = (float) $pendientes->sum('monto');
            if ($deuda <= 0) {
                return response()->json(['message' => 'La venta no tiene deuda pendiente'], 422);
            }

            $abono = min((float) $validated['monto'], $deuda);
            $restante = $abono;
            foreach ($pendientes as $pago) {
                if ($restante <= 0) {
                    break;
                }
                $montoPago = (float) $pago->monto;
                if ($montoPago <= $restante) {
                    $pago->update([
                        'user_id' => $request->user()->id ?? null,
                        'fecha_pago' => now(),
                        'metodo' => $validated['metodo'] ?? 'efectivo',
                        'observacion' => $validated['observacion'] ?: 'Amortizacion',
                        'estado' => 'PAGADO',
                    ]);
                    $restante -= $montoPago;
                } else {
                    $pago->update([
                        'monto' => round($montoPago - $restante, 2),
                        'observacion' => 'Saldo pendiente luego de amortizacion',
                    ]);
                    Pago::create([
                        'venta_id' => $venta->id,
                        'user_id' => $request->user()->id ?? null,
                        'nro_cuota' => ($pendientes->max('nro_cuota') ?? 0) + 1,
                        'monto' => round($restante, 2),
                        'fecha_programada' => now()->toDateString(),
                        'fecha_pago' => now(),
                        'metodo' => $validated['metodo'] ?? 'efectivo',
                        'estado' => 'PAGADO',
                        'observacion' => $validated['observacion'] ?: 'Amortizacion parcial',
                    ]);
                    $restante = 0;
                }
            }

            $venta->load(['pagos', 'detalles', 'user', 'caja']);
            return $this->withResumen($venta);
        });
    }

    public function ocultarDeuda(Request $request, Venta $venta)
    {
        $request->validate([
            'oculto' => 'nullable|boolean',
        ]);
        $oculto = $request->boolean('oculto', true);
        $venta->update(['deuda_oculta' => $oculto]);
        return response()->json(['message' => $oculto ? 'Deuda oculta' : 'Deuda visible']);
    }

    public function pagarCuota(Request $request, Venta $venta, Pago $pago)
    {
        if ($pago->venta_id !== $venta->id) {
            return response()->json(['message' => 'Pago no corresponde a la venta'], 422);
        }

        if ($pago->estado === 'PAGADO') {
            return response()->json(['message' => 'La cuota ya fue pagada'], 422);
        }

        $request->validate([
            'metodo' => 'nullable|string|max:30',
            'observacion' => 'nullable|string|max:255',
        ]);

        $pago->update([
            'user_id' => $request->user()->id ?? null,
            'fecha_pago' => now(),
            'metodo' => $request->metodo ?: 'credito',
            'observacion' => $request->observacion,
            'estado' => 'PAGADO',
        ]);

        return $pago;
    }

    public function anularPago(Request $request, Venta $venta, Pago $pago)
    {
        if ($pago->venta_id !== $venta->id) {
            return response()->json(['message' => 'Pago no corresponde a la venta'], 422);
        }
        if ($pago->estado !== 'PAGADO') {
            return response()->json(['message' => 'Solo se puede anular pagos en estado PAGADO'], 422);
        }

        DB::transaction(function () use ($venta, $pago) {
            $pago->update([
                'estado' => 'ANULADO',
                'observacion' => trim(($pago->observacion ? $pago->observacion . ' | ' : '') . 'Pago anulado'),
            ]);

            Pago::create([
                'venta_id' => $venta->id,
                'user_id' => null,
                'nro_cuota' => ($venta->pagos()->max('nro_cuota') ?? 0) + 1,
                'monto' => $pago->monto,
                'fecha_programada' => now()->toDateString(),
                'fecha_pago' => null,
                'metodo' => 'credito',
                'estado' => 'PENDIENTE',
                'observacion' => 'Reversion por anulacion de pago #' . $pago->id,
            ]);
        });

        $venta->load(['caja', 'detalles', 'pagos', 'user']);
        return $this->withResumen($venta);
    }

    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado === 'ANULADA') {
            return response()->json(['message' => 'La venta ya fue anulada'], 422);
        }

        // No permitir anular ingresos si dejaria la caja en negativo.
        if (!empty($venta->caja_id) && $venta->tipo_movimiento === 'ingreso') {
            $montoAnular = $this->montoQueAfectaCaja($venta);
            $saldoActual = $this->saldoCajaDisponible((int) $venta->caja_id);
            $saldoResultante = round($saldoActual - $montoAnular, 2);
            if ($saldoResultante < 0) {
                return response()->json([
                    'message' => 'No se puede anular. Fondos insuficientes en caja para revertir este ingreso.',
                    'saldo_actual' => $saldoActual,
                    'monto_anular' => $montoAnular,
                    'saldo_resultante' => $saldoResultante,
                ], 422);
            }
        }

        DB::transaction(function () use ($venta) {
            $venta->update(['estado' => 'ANULADA']);
            $venta->detalles()->update(['estado' => false]);

            // Si este movimiento pertenece a pagos de personal, mantener estados sincronizados.
            $personalPago = PersonalPago::where(function ($q) use ($venta) {
                $q->where('venta_id', $venta->id)
                    ->orWhere('venta_reversion_id', $venta->id);
            })->where('estado', 'ACTIVO')->first();

            if ($personalPago) {
                $updates = ['estado' => 'ANULADO'];
                if ((int) $personalPago->venta_reversion_id === (int) $venta->id) {
                    $updates['venta_reversion_id'] = null;
                }
                $personalPago->update($updates);
            }
        });

        return response()->json(['message' => 'Venta anulada']);
    }

    private function crearPlanCuotas(Venta $venta, Request $request, array $validated, float $total): void
    {
        $pagoInicial = (float) ($validated['pago_inicial'] ?? 0);
        $pagoInicial = min(max($pagoInicial, 0), $total);

        if ($pagoInicial > 0) {
            Pago::create([
                'venta_id' => $venta->id,
                'user_id' => $request->user()->id ?? null,
                'nro_cuota' => 0,
                'monto' => round($pagoInicial, 2),
                'fecha_programada' => now()->toDateString(),
                'fecha_pago' => now(),
                'metodo' => $validated['metodo_pago'] ?? 'efectivo',
                'estado' => 'PAGADO',
                'observacion' => 'Pago inicial',
            ]);
        }

        $saldo = round($total - $pagoInicial, 2);
        if ($saldo <= 0) {
            return;
        }
        Pago::create([
            'venta_id' => $venta->id,
            'user_id' => null,
            'nro_cuota' => 1,
            'monto' => $saldo,
            'fecha_programada' => now()->addDays(30)->toDateString(),
            'estado' => 'PENDIENTE',
            'metodo' => $validated['metodo_pago'] ?? 'credito',
            'observacion' => 'Saldo pendiente por credito',
        ]);
    }

    private function buildObservacion(array $validated): ?string
    {
        $concepto = trim((string) ($validated['concepto'] ?? ''));
        $obs = trim((string) ($validated['observacion'] ?? ''));
        if ($concepto !== '' && $obs !== '') {
            return "Concepto: {$concepto}. {$obs}";
        }
        if ($concepto !== '') {
            return "Concepto: {$concepto}";
        }
        return $obs !== '' ? $obs : null;
    }

    private function withResumen(Venta $venta): Venta
    {
        $pagado = $venta->pagos->where('estado', 'PAGADO')->sum('monto');
        $deuda = $venta->pagos->where('estado', 'PENDIENTE')->sum('monto');
        $venta->setAttribute('total_pagado', round((float)$pagado, 2));
        $venta->setAttribute('saldo_pendiente', round((float)$deuda, 2));
        return $venta;
    }

    private function montoQueAfectaCaja(Venta $venta): float
    {
        $pagado = (float) DB::table('pagos')
            ->where('venta_id', $venta->id)
            ->where('estado', 'PAGADO')
            ->sum('monto');

        if ($venta->tipo_pago === 'credito') {
            return round($pagado, 2);
        }

        if ($pagado > 0) {
            return round($pagado, 2);
        }

        return round((float) ($venta->total ?? 0), 2);
    }

    private function saldoCajaDisponible(int $cajaId): float
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
            ->where('v.estado', 'ACTIVA')
            ->where('v.caja_id', $cajaId)
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

        $ingresos = (float) ($row->ingresos ?? 0);
        $egresos = (float) ($row->egresos ?? 0);
        return round($ingresos - $egresos, 2);
    }
}
