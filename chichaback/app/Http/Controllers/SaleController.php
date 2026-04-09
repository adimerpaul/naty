<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\General;
use App\Models\Detalle;
use App\Models\Loggeneral;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
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

    public function listSale(Request $request){
        $fechaIni=$request->ini;
        $fechaFin=$request->fin;
        $page = $request->page;
        $filter = $request->filter;
        error_log($filter);
        return Venta::with('cliente')
            ->with('detalles')
            ->with('user')
            ->where('tipo',$request->tipo)
            ->whereHas('cliente', function ($query) use ($filter) {
                $query->where('local', 'like', '%'.$filter.'%')
                    ->orWhere('titular', 'like', '%'.$filter.'%');
            })
            ->whereBetween('fecha',[$fechaIni,$fechaFin])
            ->orderBy('id','desc')
            ->paginate(100);
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
        $venta= new Venta();
        $venta->fecha=$request->fecha;
        $venta->hora=date("H:i:s");
        $venta->total=$request->total;
        $venta->tipo=$request->tipo;
        $venta->acuenta=$request->acuenta;
        $venta->saldo=$request->saldo;
        $venta->estado=$request->estado;
        $venta->observacion=strtoupper($request->observacion);
        $venta->user_id=$request->user()->id;
        $venta->cliente_id=$request->cliente_id;
        $venta->save();

//        return $venta;
        foreach ($request->detalles as $detalle){
//            var_dump($detalle);
//            echo $detalle['producto_id'].'--';
            $d= new Detalle();
            $d->venta_id=$venta->id;
            $d->user_id=$request->user()->id;
            $d->producto_id=$detalle['id'];
            $d->cantidad=$detalle['cantidad'];
            $d->nombreproducto=$detalle['nombre'];
            $d->precio=$detalle['precio'];
            $d->subtotal=$detalle['subtotal'];
            $d->save();
//            echo $detalle;
        }
        $general=General::find(1);
        $general->monto=$general->monto + $venta->acuenta;
        $general->save();

        $loggeneral= new Loggeneral;
        $loggeneral->numero=$venta->id;
        $loggeneral->monto=$venta->acuenta;
        $loggeneral->detalle=$venta->tipo;
        $loggeneral->motivo='VENTA PRODUCTOS';
        $loggeneral->tipo='INGRESO';
        $loggeneral->fecha=$venta->fecha;
        $loggeneral->hora=date("H:i:s");
        $loggeneral->glosa_id=null;
        $loggeneral->user_id= $venta->user_id;
        $loggeneral->save();

        return $this->impresionVenta($venta->id);
    }

    public function impresionVenta($id){
        $venta=Venta::with('user')
        ->with('detalles')
        ->with('cliente')
        ->where('id',$id)
        ->get()[0];
         $cinit=00;
         $total=0;
         $cadena='
        <style>
        *{
            font-size:14px
        }
        .textcnt{
            text-align:center;
        }
        table{width:100%;}
        td{vertical-align:top;}
        .textgrd{font-size:20px}
        .leyenda{text-align:justify;
            size-font 6px;}
        </style>
        <hr>
        <div style="text-align: center;">Ficha de Despacho</div>
        <div style="text-align: right;">'.date('d/m/Y',strtotime($venta->fecha)).'</div>
        <div>Nro :'.$venta->id.'</div>';
        if($venta->cliente->local!='' || $venta->cliente->local!=null)
        {$cadena.='<div>Local: '.$venta->cliente->local.'</div>';}
        $cadena.='<div>Nombre: '.$venta->cliente->titular.'</div>
        <div>Usuario: '.$venta->user->name.'</div>
        <hr>
        <table>
        <thead>
        <tr><th>Cant</th><th>Prod</th><th>Subt</th></tr>
        </thead>
        <tbody>';
        foreach ($venta->detalles as $d) {
            $total+=floatval($d->subtotal);
            # code...
        $cadena.='<tr><td>'.$d->cantidad.'</td><td>'.$d->nombreproducto.'</td><td>'.$d->subtotal.'</td></tr>';
        }
        $cadena.='</tbody>
        </table>
        <div>TOTAL: <b>'.$total.' Bs</b></div>
        <div>Observacion: <b>'.$venta->observacion.'</b></div>

        <div style="color:white">-----------------</div>
        <br>
              ';
              return $cadena;
    }
    public function updateRuta(Request $request)
    {
//        $venta->update($request->all());
//        return $request;
        $venta=Venta::find($request->id);
        $venta->fechaentrega=$request->fechaentrega;
        $venta->turno=strtoupper($request->turno);
        $venta->hora=strtoupper($request->hora);
        $venta->telefono1=strtoupper($request->telefono1);
        $venta->telefono2=strtoupper($request->telefono2);
        $venta->direccion=strtoupper($request->direccion);
        $venta->observacion=strtoupper($request->observacion);
        $venta->envase=strtoupper($request->envase);
        $venta->save();
        return $this->impresionruta($venta->id);
    }
    public function impresionruta($id){
        $venta=Venta::with('user')
        ->with('detalles')
        ->with('cliente')
        ->where('id',$id)
        ->get()[0];
         $cinit=00;
        $cadena='
        <style>
        *{text-size:12px;}
        .textcnt{
            text-align:center;
        }

        table{width:100%}
        td{vertical-align:top;}
        .textgrd{font-size:20px}
        </style>
        <div class="textcnt">H/R</div>
        <div class="textcnt">Nro '.$venta->id.'</div>
        <table><tr>
        <td>Fecha: </td><td>'.date('d/m/Y',strtotime($venta->fecha)).'</td></tr>
        <tr><td>Fecha entrega: </td><td><b>'.date('d/m/Y',strtotime($venta->fechaentrega)).'</b></td>
        </tr></table>
        <hr>
        <table>
        <tr><th>CANTIDAD</th><th>PRODUCTO</th></tr>';
        foreach ($venta->detalles as $d) {
            $cadena.='<tr><td>'.$d->cantidad.'</td><td>'.$d->nombreproducto.'</td></tr>';
        }
        $cadena.='</table>
        <table>
        <tr><td>Nombre: </td><td>'.$venta->cliente->titular.'</td></tr>
        <tr><td>Tel1: </td><td>'.$venta->telefono1.'</td></tr>
        <tr><td>Tel2: </td><td>'.$venta->telefono2.'</td></tr>
        <tr><td>Direccion: </td><td>'.$venta->direccion.'</td></tr>
        <tr><td>Turno: </td><td>'.$venta->turno.'</td></tr>
        <tr><td>Hora: </td><td>'.$venta->hora.'</td></tr>
        <tr><td>Envase: </td><td><b>'.$venta->envase.'</b></td></tr>
        </table>
        <hr>
        <table>
        <tr><td>Observacion: </td><td>'.$venta->observacion.'</td></tr>
        <tr><td>Total: </td><td>'.$venta->total.'</td></tr>
        <tr><td>A cuenta: </td><td>'.$venta->acuenta.'</td></tr>
        <tr><td>Saldo: </td><td><b>'.$venta->saldo.'</b></td></tr>
        <tr><td>Usuario: </td><td>'.$venta->user->name.'</td></tr>
        </table>
              ';
              return $cadena;
    }
    public function impresionruta2($id){
        $venta=Venta::with('user')
        ->with('detalles')
        ->with('cliente')
        ->where('id',$id)
        ->get()[0];
         $cinit=00;
        $cadena='
        <style>
        *{text-size:12px;}
        .textcnt{
            text-align:center;
        }

        table{width:100%}
        td{vertical-align:top;}
        .textgrd{font-size:20px}
        </style>
        <div class="textcnt">H/R</div>
        <div class="textcnt">Nro '.$venta->id.'</div>
        <table><tr>
        <td>Fecha: </td><td>'.date('d/m/Y',strtotime($venta->fecha)).'</td></tr>
        <tr><td>Fecha entrega: </td><td><b>'.date('d/m/Y',strtotime($venta->fechaentrega)).'</b></td>
        </tr></table>
        <hr>
        <table>
        <tr><th>CANTIDAD</th><th>PRODUCTO</th></tr>';
        foreach ($venta->detalles as $d) {
            $cadena.='<tr><td>'.$venta->detalle->cantidad.'</td><td>'.$venta->detalle->nombreproducto.'</td></tr>';
        }
        $cadena.='</table>
        <table>
        <tr><td>Nombre: </td><td>'.$venta->cliente->titular.'</td></tr>
        <tr><td>Tel1: </td><td>'.$venta->telefono1.'</td></tr>
        <tr><td>Tel2: </td><td>'.$venta->telefono2.'</td></tr>
        <tr><td>Direccion: </td><td>'.$venta->direccion.'</td></tr>
        <tr><td>Turno: </td><td>'.$venta->turno.'</td></tr>
        <tr><td>Hora: </td><td>'.$venta->hora.'</td></tr>
        <tr><td>Envase: </td><td><b>'.$venta->envase.'</b></td></tr>
        </table>
        <hr>
        <table>
        <tr><td>Observacion: </td><td>'.$venta->observacion.'</td></tr>
        <tr><td>Total: </td><td>'.$venta->total.'</td></tr>
        <tr><td>A cuenta: </td><td>'.$venta->acuenta.'</td></tr>
        <tr><td>Saldo: </td><td><b>'.$venta->saldo.'</b></td></tr>
        <tr><td>Usuario: </td><td>'.$venta->user->name.'</td></tr>
        </table>
              ';
              return $cadena;
    }

    public function reportSale(Request $request){
        if($request->user_id==0){
            return Venta::with('user')
                ->with('cliente')
                ->with('pagos')
                ->with('detalles')
                ->whereDate('fecha','>=',$request->fecha1)
                ->whereDate('fecha','<=',$request->fecha2)
                ->where('estado','<>','ANULADO')
                ->orderBy('id','desc')
                ->get();
        }
        else{
            return Venta::with('user')
            ->with('cliente')
            ->with('pagos')
            ->with('detalles')
            ->whereDate('fecha','>=',$request->fecha1)
            ->whereDate('fecha','<=',$request->fecha2)
            ->where('estado','<>','ANULADO')
            ->where('user_id',$request->user_id)
            ->orderBy('id','desc')
            ->get();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function repClienteVenta(Request $request){
        return DB::SELECT("SELECT c.id,c.local,c.titular,sum(v.total) total, count(*) cantidad
        from clientes c inner join ventas v on v.cliente_id=c.id
        where v.fecha>='$request->ini' and v.fecha<='$request->fin' and v.estado!='ANULADO'
        AND c.local!='' GROUP by c.id,c.local,c.titular;");
    }

    public function repClienteVenta2(Request $request){
        return DB::SELECT("SELECT c.id,c.local,c.titular,sum(v.total) total, count(*) cantidad
        from clientes c inner join ventas v on v.cliente_id=c.id
        where v.fecha>='$request->ini' and v.fecha<='$request->fin' and v.estado!='ANULADO'
        AND c.local='' GROUP by c.id,c.local,c.titular;");
    }
}
