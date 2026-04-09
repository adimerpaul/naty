<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarantiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Garantia::with('cliente')->get();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $inventario=Inventario::find($request->inventario_id);
        if($inventario->cantidad>=$request->cantidad){
        $garantia= new Garantia();
        $garantia->fecha=date('Y-m-d');
        $garantia->efectivo=$request->efectivo;
        $garantia->fisico=$request->fisico;
        $garantia->observacion=$request->observacion;
        $garantia->estado='PRESTAMO';
        $garantia->cantidad=$request->cantidad;
        $garantia->inventario_id=$request->inventario_id;
        $garantia->cliente_id=$request->cliente_id;
        $garantia->user_id=$request->user()->id;
        $garantia->save();
        $inventario->cantidad-=$request->cantidad;
        $inventario->save();
        return true;
        }
        else return false;

    }

    public function devolver(Request $request){
        $garantia=Garantia::find($request->id);
        $garantia->fechadev=date('Y-m-d');
        $garantia->observacion=$request->observacion;
        $garantia->estado='DEVUELTO';
        $garantia->userdev_id=$request->user()->id;
        $garantia->save();
        $inventario=Inventario::find($garantia->inventario_id);
        $inventario->cantidad+=intval($garantia->cantidad);
        $inventario->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Garantia  $garantia
     * @return \Illuminate\Http\Response
     */
    public function show(Garantia $garantia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Garantia  $garantia
     * @return \Illuminate\Http\Response
     */
    public function edit(Garantia $garantia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Garantia  $garantia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Garantia $garantia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Garantia  $gara  ntia
     * @return \Illuminate\Http\Response    
     */     
    public function destroy(Garantia $garantia)
    {
        //
    }

    public function listaprestamo(Request $request){
        return DB::select("
        SELECT g.id as id,c.titular as titular,g.fecha as fecha,i.nombre as nombre,g.efectivo as efectivo,g.fisico as fisico,
        g.observacion as observacion,u.name as name, g.estado as estado, g.cantidad as cantidad
         from garantias g inner join clientes c on g.cliente_id=c.id
INNER JOIN users u on g.user_id= u.id
INNER JOIN inventarios i on g.inventario_id= i.id
where date(g.fecha)>='".$request->fecha1."' and date(g.fecha)<='".$request->fecha2."'
and c.id='".$request->cliente_id."'");
        
    }

    public function impgarantia($id){
        $garantia=Garantia::with('cliente')->get();
        $cadena='
        <style>
        .textc{text-align:center}
        </style>
        <div>N '.$garantia->id.'</div>
        <div>Nombre: '.$garantia->cliente->titular.'</div>
        <div>Telefono: '.$garantia->cliente->telefono.'</div>
        <div>Efectivo '.$garantia->Efectivo.'</div>
        <div>Detalle '.$garantia->fisico.'</div>
        <div>Fecha '.$garantia->fecha.'</div>
        <br>
        <div class="textc">Firma</div>
        ';
        return $cadena;
    }
}
