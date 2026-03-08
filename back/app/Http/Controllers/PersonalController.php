<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonalController extends Controller
{
    public function index()
    {
        return Personal::query()->orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:255|unique:personales,ci',
            'nombre' => 'required|string|max:255',
            'salario' => 'nullable|numeric|min:0',
            'fechanac' => 'nullable|date',
            'dias' => 'nullable|integer|min:0',
            'celular' => 'nullable|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'fechaingreso' => 'nullable|date',
            'estado' => ['nullable', Rule::in(['ACTIVO', 'INACTIVO'])],
            'fotografia' => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('fotografia')) {
            $validated['fotografia'] = $this->savePhoto($request->file('fotografia'));
        }

        return Personal::create($validated);
    }

    public function update(Request $request, Personal $personal)
    {
        $validated = $request->validate([
            'ci' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('personales', 'ci')->ignore($personal->id)],
            'nombre' => 'sometimes|required|string|max:255',
            'salario' => 'nullable|numeric|min:0',
            'fechanac' => 'nullable|date',
            'dias' => 'nullable|integer|min:0',
            'celular' => 'nullable|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'fechaingreso' => 'nullable|date',
            'estado' => ['nullable', Rule::in(['ACTIVO', 'INACTIVO'])],
            'fotografia' => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('fotografia')) {
            $validated['fotografia'] = $this->savePhoto($request->file('fotografia'));
        }

        $personal->update($validated);

        return $personal;
    }

    public function destroy(Personal $personal)
    {
        $personal->delete();
        return response()->json(['message' => 'Personal eliminado']);
    }

    private function savePhoto($file): string
    {
        $dir = public_path('images/personales');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return $filename;
    }
}

