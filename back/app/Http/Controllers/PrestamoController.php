<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\PrestamoRetorno;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestamo::with(['cliente', 'inventario', 'venta', 'retornos.user'])->orderBy('id', 'desc');
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('tipo_venta')) {
            $tipoVenta = $request->validate([
                'tipo_venta' => ['nullable', Rule::in(['detalle', 'local'])],
            ])['tipo_venta'] ?? null;

            if ($tipoVenta) {
                $query->whereHas('cliente', fn ($q) => $q->where('tipo_cliente', $tipoVenta));
            }
        }

        return $query->get()->map(fn (Prestamo $prestamo) => $this->withResumen($prestamo))->values();
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
            $cliente = Cliente::findOrFail($validated['cliente_id']);
            if (! empty($validated['tipo_venta']) && $cliente->tipo_cliente !== $validated['tipo_venta']) {
                throw ValidationException::withMessages([
                    'cliente_id' => 'El cliente no corresponde al tipo de prestamo seleccionado.',
                ]);
            }

            $inv = Inventario::lockForUpdate()->findOrFail($validated['inventario_id']);
            if ($inv->cantidad < (int) $validated['cantidad']) {
                return response()->json(['message' => 'No hay cantidad suficiente en inventario'], 422);
            }

            $inv->update(['cantidad' => $inv->cantidad - (int) $validated['cantidad']]);

            $ventaGenerada = null;
            $montoPrestamo = $this->montoFisicoRecibido($validated);
            if ($validated['tipo'] === 'venta') {
                $monto = $montoPrestamo;
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
                    'observacion' => 'Venta de material inventario. '.($validated['observacion'] ?? ''),
                    'total' => round($monto, 2),
                ]);
                VentaDetalle::create([
                    'venta_id' => $ventaGenerada->id,
                    'producto_id' => null,
                    'user_id' => $request->user()->id ?? null,
                    'producto_nombre' => "Material: {$inv->nombre}",
                    'precio' => round($monto / max((int) $validated['cantidad'], 1), 2),
                    'cantidad' => (int) $validated['cantidad'],
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
                'efectivo' => $montoPrestamo,
                'fisico' => $validated['fisico'] ?? '',
                'observacion' => $validated['observacion'] ?? '',
                'cantidad' => (int) $validated['cantidad'],
                'prestado' => $validated['tipo'] === 'venta',
                'user_id' => $request->user()->id ?? null,
                'cliente_id' => $validated['cliente_id'],
                'inventario_id' => $validated['inventario_id'],
                'venta_id' => $validated['venta_id'] ?? $ventaGenerada?->id,
            ]);

            return $this->withResumen($prestamo->load(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function retornar(Request $request, Prestamo $prestamo)
    {
        $prestamo->loadMissing('retornos');
        if (! in_array($prestamo->estado, ['EN PRESTAMO', 'PARCIAL'], true)) {
            return response()->json(['message' => 'Solo se puede retornar prestamos pendientes o parciales'], 422);
        }

        return DB::transaction(function () use ($prestamo, $request) {
            $prestamo->refresh()->load('retornos');
            $saldoCantidad = $this->saldoCantidad($prestamo);
            $saldoEfectivo = $this->saldoEfectivo($prestamo);

            if ($saldoCantidad <= 0 && $saldoEfectivo <= 0) {
                return response()->json(['message' => 'El prestamo ya fue retornado completamente'], 422);
            }

            $this->registrarRetorno($prestamo, $request, $saldoCantidad, $saldoEfectivo, null, 'Retorno completo');

            $prestamo->update([
                'estado' => 'RETORNADO',
                'prestado' => false,
                'observacion' => trim(($prestamo->observacion ? $prestamo->observacion.' | ' : '').'Retornado completo'),
            ]);

            return $this->withResumen($prestamo->load(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    public function retornoParcial(Request $request, Prestamo $prestamo)
    {
        $validated = $request->validate([
            'cantidad' => 'nullable|integer|min:0',
            'efectivo' => 'nullable|numeric|min:0',
            'fisico' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:255',
        ]);

        $prestamo->loadMissing('retornos');
        if (! in_array($prestamo->estado, ['EN PRESTAMO', 'PARCIAL'], true)) {
            return response()->json(['message' => 'Solo se puede retornar prestamos pendientes o parciales'], 422);
        }

        return DB::transaction(function () use ($prestamo, $request, $validated) {
            $prestamo->refresh()->load('retornos');
            $cantidad = (int) ($validated['cantidad'] ?? 0);
            $efectivo = $this->montoFisicoRecibido($validated);
            $fisico = trim((string) ($validated['fisico'] ?? ''));

            if ($cantidad <= 0 && $efectivo <= 0 && $fisico === '') {
                throw ValidationException::withMessages([
                    'cantidad' => 'Debe registrar cantidad o fisico retornado.',
                ]);
            }

            $saldoCantidad = $this->saldoCantidad($prestamo);
            $saldoEfectivo = $this->saldoEfectivo($prestamo);

            if ($cantidad > $saldoCantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad supera el saldo pendiente.',
                ]);
            }
            if ($efectivo > $saldoEfectivo) {
                throw ValidationException::withMessages([
                    'efectivo' => 'El fisico retornado supera el saldo pendiente.',
                ]);
            }

            $this->registrarRetorno(
                $prestamo,
                $request,
                $cantidad,
                $efectivo,
                $fisico,
                $validated['observacion'] ?? null
            );

            $prestamo->refresh()->load('retornos');
            $nuevoEstado = $this->saldoCantidad($prestamo) <= 0 && $this->saldoEfectivo($prestamo) <= 0
                ? 'RETORNADO'
                : 'PARCIAL';

            $prestamo->update([
                'estado' => $nuevoEstado,
                'prestado' => $nuevoEstado !== 'RETORNADO',
            ]);

            return $this->withResumen($prestamo->load(['cliente', 'inventario', 'venta', 'retornos.user']));
        });
    }

    private function registrarRetorno(
        Prestamo $prestamo,
        Request $request,
        int $cantidad,
        float $efectivo,
        ?string $fisico,
        ?string $observacion
    ): PrestamoRetorno {
        if ($cantidad > 0) {
            $inventario = Inventario::lockForUpdate()->findOrFail($prestamo->inventario_id);
            $inventario->update([
                'cantidad' => (int) $inventario->cantidad + $cantidad,
            ]);
        }

        return PrestamoRetorno::create([
            'prestamo_id' => $prestamo->id,
            'user_id' => $request->user()->id ?? null,
            'fecha' => now()->toDateString(),
            'cantidad' => $cantidad,
            'efectivo' => round($efectivo, 2),
            'fisico' => $fisico,
            'observacion' => $observacion,
        ]);
    }

    private function withResumen(Prestamo $prestamo): Prestamo
    {
        $prestamo->loadMissing('retornos');
        $retornadoCantidad = (int) $prestamo->retornos->sum('cantidad');
        $retornadoEfectivo = round((float) $prestamo->retornos->sum('efectivo'), 2);
        $fisicoRecibido = $this->montoPrestamo($prestamo);

        $prestamo->setAttribute('retornado_cantidad', $retornadoCantidad);
        $prestamo->setAttribute('cantidad_actual', max(0, (int) $prestamo->cantidad - $retornadoCantidad));
        $prestamo->setAttribute('retornado_efectivo', $retornadoEfectivo);
        $prestamo->setAttribute('efectivo_actual', max(0, round($fisicoRecibido - $retornadoEfectivo, 2)));
        $prestamo->setAttribute('fisico_recibido', $fisicoRecibido);
        $prestamo->setAttribute('monto_pendiente', max(0, round($fisicoRecibido - $retornadoEfectivo, 2)));

        return $prestamo;
    }

    private function saldoCantidad(Prestamo $prestamo): int
    {
        $prestamo->loadMissing('retornos');

        return max(0, (int) $prestamo->cantidad - (int) $prestamo->retornos->sum('cantidad'));
    }

    private function saldoEfectivo(Prestamo $prestamo): float
    {
        $prestamo->loadMissing('retornos');

        return max(0, round($this->montoPrestamo($prestamo) - (float) $prestamo->retornos->sum('efectivo'), 2));
    }

    private function montoFisicoRecibido(array $validated): float
    {
        $monto = round((float) ($validated['efectivo'] ?? 0), 2);
        if ($monto <= 0 && isset($validated['fisico']) && is_numeric($validated['fisico'])) {
            $monto = round((float) $validated['fisico'], 2);
        }

        return $monto;
    }

    private function montoPrestamo(Prestamo $prestamo): float
    {
        $monto = round((float) $prestamo->efectivo, 2);
        if ($monto <= 0 && is_numeric($prestamo->fisico)) {
            $monto = round((float) $prestamo->fisico, 2);
        }

        return $monto;
    }
}
