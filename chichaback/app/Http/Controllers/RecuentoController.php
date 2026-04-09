<?php

namespace App\Http\Controllers;

use App\Models\Recuento;
use App\Models\Material;
use App\Models\Compra;
use Illuminate\Http\Request;

class RecuentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //return Recuento::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function consulrecuento(Request $request)
    {
        //
        return Recuento::with('material')->where('material_id',$request->material_id)
        ->whereDate('fecha','>=',$request->fecha1)->where('fecha','<=',$request->fecha2)->get();
    }
    public function consulrecuento2(Request $request)
    {
        //
        return Recuento::with('material')
//            ->where('material_id',$request->material_id)
            ->whereDate('fecha','>=',$request->fecha1)
            ->where('fecha','<=',$request->fecha2)
            ->get();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $compra = Compra::find($request->compra_id);
        if(($compra->cantidad - $compra->retiro) >= $request->cantidad){
        $material=Material::find($request->material_id);
        $material->stock=$material->stock - $request->cantidad;
        $material->save();

        $recuento=new Recuento;
        $recuento->fecha=date('Y-m-d');
        $recuento->hora=date('H:i:s');
        $recuento->cantidad=$request->cantidad;
        $recuento->observacion=$request->observacion;
        $recuento->material_id=$request->material_id;
        $recuento->compra_id=$request->compra_id;
        $recuento->user_id=Request()->user()->id;
        $recuento->save();

        $compra->retiro = $compra->retiro + $request->cantidad;
        $compra->save();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recuento  $recuento
     * @return \Illuminate\Http\Response
     */
    public function show(Recuento $recuento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recuento  $recuento
     * @return \Illuminate\Http\Response
     */
    public function edit(Recuento $recuento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recuento  $recuento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recuento $recuento)
    {
        //
        $recuento=Recuento::find($request->id);
        $material=Material::find($recuento->material_id);
        if($recuento->cantidad != $request->cantidad){
            $material->stock=$material->stock + $recuento->cantidad - $request->cantidad;
            $material->save();
        }
        $recuento->cantidad=$request->cantidad;
        $recuento->observacion=$request->observacion;
        $recuento->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recuento  $recuento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $recuento=Recuento::find($id);
        $compra=Compra::find($recuento->compra_id);
        $compra->retiro = $compra->retiro - $recuento->cantidad;
        $compra->save();

        $material=Material::find($recuento->material_id);
        $material->stock=$material->stock + $recuento->cantidad;
        $material->save();
        $recuento->delete();
    }
}
