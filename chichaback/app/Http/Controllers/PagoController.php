<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Venta;
use App\Models\General;
use App\Models\Loggeneral;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Detalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $pago=new Pago;
        $pago->fecha=$request->fecha;
        $pago->venta_id=$request->venta_id;
        $pago->user_id=$request->user()->id;
        $pago->monto=$request->monto;
        $pago->observacion=$request->observacion;
        $pago->save();

        $venta=Venta::find($request->venta_id);
        $venta->saldo=$venta->saldo - $request->monto;

        $general=General::find(1);
        $general->monto=$general->monto +  $request->monto;
        $general->save();

        $loggeneral= new Loggeneral;
        $loggeneral->numero=$venta->id;
        $loggeneral->monto= $request->monto;
        $loggeneral->detalle='CXC '.$venta->tipo;
        $loggeneral->motivo='PAGO CXC';
        $loggeneral->tipo='INGRESO';
        $loggeneral->fecha=$request->fecha;
        $loggeneral->hora=date("H:i:s");
        $loggeneral->glosa_id=null;
        $loggeneral->user_id=$request->user()->id;
        $loggeneral->save();

        if($venta->saldo==0)
        $venta->estado='CANCELADO';
        $venta->save();
        
        $usuario=User::find($request->user()->id);
        $cliente=Cliente::find($venta->cliente_id);
        $detalle=Detalle::where('venta_id',$venta->id)->first();

        if($cliente->local == null || $cliente->local=='')  $cliente->local=' ';
        if($pago->observacion == null || $pago->observacion=='') $pago->observacion=' ';
        $cadena=
        " <style>
        .textcnt{
            text-align:center;
        }
        table{width:100%;}
        td{vertical-align:top;}
        </style>
        <div class='textcnt'> DETALLE CUENTA POR PAGAR</div>
        <div class='textcnt'>Nro ".$venta->id."</div>
        <hr>
        <div>Usuario   : ".$usuario->name."</div>
        <div>Fec Reg   : ".date('d/m/Y',strtotime($venta->fecha))."</div>
        <div>Local     : ".$cliente->local."</div>
        <div>Titular   : ".$cliente->titular."</div>
        <div>Producto  : ".$detalle->nombreproducto."</div>
        <div>Cantidad  : ".$detalle->cantidad."</div>
        <div>Costo     : ".$detalle->subtotal."</div>
        <hr>
        <table>
        <tr><td>Fecha: </td><td><b>".date('d/m/Y',strtotime($pago->fecha))."</b></td></tr>
        <tr><td>Monto: </td><td><b>".$pago->monto."</b></td></tr>
        <tr><td>Saldo: </td><td><b>".$venta->saldo."</b></td></tr>
        <tr><td>Observacion: </td><td><b>".$pago->observacion."</b></td></tr>
        </table>
        <br>";

        return $cadena;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function show(Pago $pagos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function edit(Pago $pagos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pago $pagos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pagos  $pagos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $pago=Pago::find($id);
        //$pago->save();

        $venta=Venta::find($pago->venta_id);
        $venta->saldo=$venta->saldo + $pago->monto;

        $general=General::find(1);
        $general->monto=$general->monto -  $pago->monto;
        $general->save();

        $loggeneral= Loggeneral::where('numero',$venta->id)
        ->where('detalle','CXC '.$venta->tipo)
        ->where('motivo','PAGO CXC')
        ->where('tipo','INGRESO')
        ->first();
        $loggeneral->delete();

        $venta->estado='POR COBRAR';
        $pago->delete();
        $venta->save();
    }

    public function reportepago(Request $request){
        //if($request->user()->id==1)
        //{
            if($request->id==0){
                return Pago::with('user')->with('venta')->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->get();
            /*return DB::SELECT("SELECT v.id,p.fecha,v.fechaentrega,p.monto,v.tipo, c.local,c.titular
            FROM pagos p inner join ventas v on p.venta_id=v.id inner join detalles d ON v.id=d.venta_id
            inner join clientes c on v.cliente_id=c.id
            where date(p.fecha)>='$request->fecha1' and date(p.fecha)<='$request->fecha2'");*/
        }
            else{
                return Pago::with('user')->with('venta')->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->where('user_id',$request->id)->get();
            /*return DB::SELECT("SELECT v.id,p.fecha,v.fechaentrega,p.monto,v.tipo, c.local,c.titular
            FROM pagos p inner join ventas v on p.venta_id=v.id inner join detalles d ON v.id=d.venta_id
            inner join clientes c on v.cliente_id=c.id
            where p.user_id=$request->id and
            date(p.fecha)>='$request->fecha1' and date(p.fecha)<='$request->fecha2'");*/
            }

        //}
        //else
        //return DB::SELECT("SELECT v.id,p.fecha,v.fechaentrega,p.monto,v.tipo,d.cantidad,d.nombreproducto, c.local,c.titular
        //FROM pagos p inner join ventas v on p.venta_id=v.id inner join detalles d ON v.id=d.venta_id
        //inner join clientes c on v.cliente_id=c.id
        //where p.user_id=".$request->user()->id." and
        //date(p.fecha)>='$request->fecha1' and date(p.fecha)<='$request->fecha2'");


    }


}
