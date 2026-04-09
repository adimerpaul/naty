<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Loginventario;

class ProductoController extends Controller
{
    public function index()
    {
        return Producto::orderBy('estado', 'ASC')->orderBy('nombre', 'ASC')->get();
    }

    public function listaproducto()
    {
        return Producto::where('estado', 'ACTIVO')->orderBy('orden', 'ASC')->get();
    }

    public function listaproducto2($type)
    {
        return Producto::where('estado', 'ACTIVO')->where('tipo', $type)->orderBy('orden', 'ASC')->orderBy('nombre', 'ASC')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producto = Producto::create($request->all());
        return $producto;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function show(Producto $producto)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function edit(Producto $producto)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $producto->update($request->all());
        return $producto;
    }

    public function uporden(Request $request)
    {
        $producto = Producto::find($request->id);
        $producto->orden = $request->orden;
        $producto->save();
    }

    public function activarprod(Request $request)
    {
        $producto = Producto::find($request->id);
        if ($producto->estado == 'ACTIVO')
            $producto->estado = 'INACTIVO';
        else {
            $producto->estado = 'ACTIVO';
        }
        $producto->save();
        return $producto;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);
        $producto->delete();
        return response()->json(['res' => 'Borrado exitoso'], 200);
    }

    public function productadd(Request $request)
    {
        $log = new Loginventario();
        $log->cantidad = $request->cantidad;
        $log->producto_id = $request->id;
        $log->fecha = date('Y-m-d');
        $log->agregar = true;
        $log->user_id = $request->user()->id;
        $log->motivo = $request->motivo;
        $log->save();

        $inventario = Producto::find($request->id);
        $inventario->cantidad += $request->cantidad;
        return $inventario->save();
    }

    public function productsub(Request $request)
    {
        $log = new Loginventario();
        $log->cantidad = $request->cantidad;
        $log->producto_id = $request->id;
        $log->agregar = false;
        $log->fecha = date('Y-m-d');
        $log->user_id = $request->user()->id;
        $log->motivo = $request->motivo;
        $log->save();

        $inventario = Producto::find($request->id);
        if ($inventario->cantidad < $request->cantidad)
            $inventario->cantidad = 0;
        else
            $inventario->cantidad -= $request->cantidad;
        return $inventario->save();
    }
}
