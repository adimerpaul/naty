<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'nombre' => 'nullable|string|max:255',
            'local' => 'nullable|string|max:255',
            'titular' => 'nullable|string|max:255',
            'tipo' => ['nullable', Rule::in(['PROPIETARIO', 'INQUILINO'])],
            'tipo_cliente' => ['required', Rule::in(['detalle', 'local'])],
            'ci' => 'nullable|string|max:255|unique:clientes,ci',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'fechanac' => 'nullable|date',
            'legalidad' => ['nullable', Rule::in(['CON LICENCIA', 'SIN LICENCIA'])],
            'categoria' => ['nullable', Rule::in(['SIMPLIFICADO', 'GENERAL', 'SIN NIT'])],
            'razon' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:2000',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'estado' => 'nullable|boolean',
        ]);

        $this->applyClienteRules($validated);
        return Cliente::create($validated);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|nullable|string|max:255',
            'local' => 'nullable|string|max:255',
            'titular' => 'nullable|string|max:255',
            'tipo' => ['nullable', Rule::in(['PROPIETARIO', 'INQUILINO'])],
            'tipo_cliente' => ['sometimes', 'required', Rule::in(['detalle', 'local'])],
            'ci' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'fechanac' => 'nullable|date',
            'legalidad' => ['nullable', Rule::in(['CON LICENCIA', 'SIN LICENCIA'])],
            'categoria' => ['nullable', Rule::in(['SIMPLIFICADO', 'GENERAL', 'SIN NIT'])],
            'razon' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:255',
            'observacion' => 'nullable|string|max:2000',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'estado' => 'nullable|boolean',
        ]);

        $existeCi = Cliente::where('ci', $validated['ci'] ?? null)
            ->where('id', '!=', $cliente->id)
            ->exists();
        if ($existeCi) {
            return response()->json(['message' => 'El CI ya está registrado para otro cliente'], 422);
        }

        $validated['tipo_cliente'] = $validated['tipo_cliente'] ?? $cliente->tipo_cliente;
        $this->applyClienteRules($validated);
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
            $v->setAttribute('total_pagado', round((float) $pagado, 2));
            $v->setAttribute('saldo_pendiente', round((float) $deuda, 2));
            return $v;
        });

        return $ventas->values();
    }

    private function applyClienteRules(array &$validated): void
    {
        $tipoCliente = $validated['tipo_cliente'] ?? 'detalle';
        $titular = trim((string) ($validated['titular'] ?? ''));
        $local = trim((string) ($validated['local'] ?? ''));

        if ($tipoCliente === 'detalle' && $titular === '') {
            throw ValidationException::withMessages([
                'titular' => 'Para cliente detalle, titular es obligatorio',
            ]);
        }

        if ($tipoCliente === 'local') {
            foreach (['local', 'titular', 'tipo', 'legalidad', 'categoria'] as $campo) {
                if (trim((string) ($validated[$campo] ?? '')) === '') {
                    throw ValidationException::withMessages([
                        $campo => "Para cliente local, {$campo} es obligatorio",
                    ]);
                }
            }
        }

        if ($tipoCliente === 'detalle') {
            $validated['nombre'] = $titular !== '' ? $titular : ($validated['nombre'] ?? null);
        } else {
            $validated['nombre'] = $local !== '' ? $local : ($validated['nombre'] ?? null);
        }
    }
}
