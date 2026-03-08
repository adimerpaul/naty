<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query()->orderBy('id', 'desc');

        if ($request->filled('tipo_cliente')) {
            $query->where('tipo_cliente', $request->tipo_cliente);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_cliente' => ['required', Rule::in(['detalle', 'local'])],
            'ci' => 'nullable|string|max:255|unique:clientes,ci',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:2000',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'estado' => 'nullable|boolean',
        ]);

        return Cliente::create($validated);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'tipo_cliente' => ['sometimes', 'required', Rule::in(['detalle', 'local'])],
            'ci' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('clientes', 'ci')->ignore($cliente->id),
            ],
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:2000',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'estado' => 'nullable|boolean',
        ]);

        $cliente->update($validated);

        return $cliente;
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json(['message' => 'Cliente eliminado']);
    }

    public function pdf(Request $request)
    {
        $query = Cliente::query()->orderBy('id', 'desc');

        if ($request->filled('tipo_cliente')) {
            $query->where('tipo_cliente', $request->tipo_cliente);
        }

        $clientes = $query->get();
        $tipo = $request->tipo_cliente ?: 'todos';

        $pdf = Pdf::loadView('pdf.clientes', [
            'clientes' => $clientes,
            'tipo' => $tipo,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('clientes-' . $tipo . '.pdf');
    }

    public function historial(Cliente $cliente, Request $request)
    {
        $query = Venta::with(['detalles', 'pagos', 'user'])
            ->where('cliente_id', $cliente->id)
            ->orderBy('id', 'desc');

        if ($request->filled('tipo_venta')) {
            $query->where('tipo_venta', $request->tipo_venta);
        }

        $ventas = $query->get()->map(function (Venta $v) {
            $pagado = $v->pagos->where('estado', 'PAGADO')->sum('monto');
            $deuda = $v->pagos->where('estado', 'PENDIENTE')->sum('monto');
            $v->setAttribute('total_pagado', round((float)$pagado, 2));
            $v->setAttribute('saldo_pendiente', round((float)$deuda, 2));
            return $v;
        });

        return $ventas->values();
    }
}
