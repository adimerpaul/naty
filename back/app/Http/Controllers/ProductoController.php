<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query()->orderBy('orden')->orderBy('id', 'desc');

        if ($request->filled('tipo_producto')) {
            $query->where('tipo_producto', $request->tipo_producto);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
            'grupo' => ['required', Rule::in(['chicha', 'garapina'])],
            'tipo_producto' => ['required', Rule::in(['detalle', 'local'])],
            'orden' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:20',
            'estado' => 'nullable|boolean',
            'fotografia' => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('fotografia')) {
            $validated['fotografia'] = $this->savePhoto($request->file('fotografia'));
        }

        return Producto::create($validated);
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'precio' => 'sometimes|required|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
            'grupo' => ['sometimes', 'required', Rule::in(['chicha', 'garapina'])],
            'tipo_producto' => ['sometimes', 'required', Rule::in(['detalle', 'local'])],
            'orden' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:20',
            'estado' => 'nullable|boolean',
            'fotografia' => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('fotografia')) {
            $validated['fotografia'] = $this->savePhoto($request->file('fotografia'));
        }

        $producto->update($validated);

        return $producto;
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    private function savePhoto($file): string
    {
        $dir = public_path('images/productos');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return $filename;
    }
}

