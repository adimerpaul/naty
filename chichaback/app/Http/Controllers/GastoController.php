<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Caja;
use App\Models\Logcaja;
use App\Models\General;
use App\Models\Loggeneral;
use App\Models\User;
use App\Models\Glosa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
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
    public function misgastos(Request  $request){
        //if($request->user()->id==1)
        //{
            if($request->user_id==0) 
            return Gasto::with('glosa')->with('user')->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->orderBy('id','desc')->get();
            else 
            return Gasto::with('glosa')->with('user')->where('user_id',$request->user_id)->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->orderBy('id','desc')->get();
       // }
        //else
        //return Gasto::with('glosa')->with('user')->where('user_id',$request->user()->id)->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->orderBy('id','desc')->get();
        //return Gasto::with('user')->whereDate('fecha','>=',$request->fecha1)->whereDate('fecha','<=',$request->fecha2)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $gasto=new Gasto();
        $gasto->precio=$request->precio;
        $gasto->observacion=$request->observacion;
        $gasto->glosa=$request->glosa;
        $gasto->fecha=$request->fecha;
        $gasto->hora=date('H:i:s');
        $gasto->glosa_id=$request->glosa_id;
        $gasto->user_id=$request->user()->id;
        $gasto->save();

        $general=General::find(1);
        $general->monto=$general->monto - $request->precio;
        $general->save();

        $loggeneral= new Loggeneral;
        $loggeneral->numero=$gasto->id;
        $loggeneral->monto= $request->precio;
        $loggeneral->detalle='GASTO';
        $loggeneral->motivo=$request->observacion;
        $loggeneral->tipo='EGRESO';
        $loggeneral->fecha=$gasto->fecha;
        $loggeneral->hora=date("H:i:s");
        $loggeneral->glosa_id=$request->glosa_id;
        $loggeneral->user_id=$request->user()->id;
        $loggeneral->save();

        $usuario=User::find($gasto->user_id);

        $cadena='
        <style>
        .textcnt{
            text-align:center;
        }
        table{width:100%;}
        td{vertical-align:top;}
        </style>
        <div class="textcnt"> DETALLE GASTO</div>
        <div class="textcnt">Nro '.$gasto->id.'</div>
        <hr>
        <div>Nombre: '.$usuario->name.'</div>
        <div>Fecha: '.date('d/m/Y',strtotime($gasto->fecha)).'</div>
        <hr>
        <table>
        <tr><td>Glosa: </td><td><b>'.$gasto->glosa.'</b></td></tr>
        <tr><td>Costo: </td><td><b>'.$gasto->precio.'</b></td></tr>
        <tr><td>Observacion: </td><td><b>'.$gasto->observacion.'</b></td></tr>
        </table>
        <div style="color:white">-----------------</div>
        <br>
              ';
              return $cadena;
    }

    public function gastocaja(Request $request)
    {
        /*$gasto=new Gasto();
        $gasto->precio=$request->precio;
        $gasto->observacion=$request->observacion;
        $gasto->glosa='CAJA CHICA';
        $gasto->fecha=date('Y-m-d');
        $gasto->hora=date('H:i:s');
        $gasto->user_id=$request->user()->id;
        $gasto->save();*/

        $caja=Caja::find(1);
        $caja->monto= floatval($caja->monto) - floatval($request->precio);
        $caja->save();

        $log=new Logcaja ;
        $log->monto=$request->precio;
        $log->motivo=$request->observacion;
        $log->tipo='GASTO';
        $log->fecha=date('Y-m-d');
        $log->hora=date('H:i:s');
        $log->glosa_id=$request->glosa_id;
        $log->user_id=$request->user()->id;
        $log->save();

        $usuario=User::find( $log->user_id);
        $glosa=Glosa::find($log->glosa_id);
        $cadena='
        <style>
        .textcnt{
            text-align:center;
        }
        table{width:100%;}
        td{vertical-align:top;}
        </style>
        <div class="textcnt"> DETALLE GASTO CAJA CHICA</div>
        <div class="textcnt">Nro '.$log->id.'</div>
        <hr>
        <div>Nombre: '.$usuario->name.'</div>
        <div>Fecha: '.date('d/m/Y',strtotime($log->fecha)).'</div>
        <hr>
        <table>
        <tr><td>Glosa: </td><td><b>'.$glosa->nombre.'</b></td></tr>
        <tr><td>Costo: </td><td><b>'.$log->monto.'</b></td></tr>
        <tr><td>Observacion: </td><td><b>'.$log->motivo.'</b></td></tr>
        </table>
        <div style="color:white">-----------------</div>
        <br>
              ';
              return $cadena;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gasto  $gasto
     * @return \Illuminate\Http\Response
     */
    public function show(Gasto $gasto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gasto  $gasto
     * @return \Illuminate\Http\Response
     */
    public function edit(Gasto $gasto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gasto  $gasto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gasto $gasto)
    {
        //$gasto->update($request->all());
        $gasto=Gasto::find($request->id);
        $gasto->precio=$request->precio;
        $aux=$gasto->precio;
        $gasto->observacion=$request->observacion;
        $gasto->glosa=$request->glosa;
        $gasto->fecha=$request->fecha;
        $gasto->glosa_id=$request->glosa_id;
        $gasto->save();

        $general=General::find(1);
        $general->monto=$general->monto - $request->precio + $aux;
        $general->save();

        $loggeneral= Loggeneral::where('numero',$gasto->id)->where('detalle','GASTO')->where('tipo','EGRESO')->get()[0];
        $loggeneral->monto= $request->precio;
        $loggeneral->motivo=$request->observacion;
        $loggeneral->fecha=$gasto->fecha;
        $loggeneral->hora=date("H:i:s");
        $loggeneral->glosa_id=$request->glosa_id;
        $loggeneral->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gasto  $gasto
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gasto=Gasto::find($id);
        $loggeneral= Loggeneral::where('numero',$gasto->id)->where('detalle','GASTO')->where('tipo','EGRESO')->first();
        if(!$loggeneral){
            return;
        }
        $general=General::find(1);
        $general->monto=$general->monto + $gasto->precio ;
        $general->save();

        $loggeneral->delete();
        $gasto->delete();
    }

    public function listglosa(){
        return DB::SELECT('SELECT glosa from gastos group by glosa');
    }
}
