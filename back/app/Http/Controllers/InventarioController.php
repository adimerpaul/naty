<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

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
            'cantidad' => 'required|integer|min:0',
            'detalle' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'estado' => 'nullable|string|max:30',
            'precio' => 'nullable|numeric|min:0',
        ]);
        return Inventario::create($validated);
    }

    public function update(Request $request, Inventario $inventario)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:255',
            'fecha' => 'nullable|date',
            'nombre' => 'sometimes|required|string|max:255',
            'cantidad' => 'sometimes|required|integer|min:0',
            'detalle' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'estado' => 'nullable|string|max:30',
            'precio' => 'nullable|numeric|min:0',
        ]);
        $inventario->update($validated);
        return $inventario;
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return response()->json(['message' => 'Inventario eliminado']);
    }
}

