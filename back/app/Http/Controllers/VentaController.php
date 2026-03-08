<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pago;
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

        return $query->get();
    }

    public function show(Venta $venta)
    {
        return $venta->load(['caja', 'detalles', 'pagos', 'user']);
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

        return $venta->load(['caja', 'detalles', 'pagos', 'user']);
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

            return $venta->load(['caja', 'detalles', 'pagos', 'user']);
        });
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

    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado === 'ANULADA') {
            return response()->json(['message' => 'La venta ya fue anulada'], 422);
        }

        DB::transaction(function () use ($venta) {
            $venta->update(['estado' => 'ANULADA']);
            $venta->detalles()->update(['estado' => false]);
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
}
