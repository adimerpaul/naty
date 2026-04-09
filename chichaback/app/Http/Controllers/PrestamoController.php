<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Inventario;
use App\Models\Logprestamo;
use App\Models\General;
use App\Models\Loggeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{
    /**.
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Prestamo::with('cliente')->with('user')->with('inventario')->with('logprestamos')->orderBy('id','desc')->get();
    }

    public function listPag(Request $request){
        {
            //

            $page = $request->page;
            $estado = $request->estado;
            $filter = $request->filter;
            $tipo= $request->tipo;
            error_log($filter);
            if ($estado=='TODO'){
            return Prestamo::with("cliente")
            ->with('user')
            ->with('inventario')
            ->with('logprestamos')
            ->whereHas('cliente', function ($query) use ($filter,$tipo) {
                $query->where('tipocliente',$tipo);
                $query->where(function ($query2) use($filter){
                    $query2->where('local', 'like', '%'.$filter.'%')
                    ->orWhere('titular', 'like', '%'.$filter.'%');});
                })
            ->orderBy('id','desc')->paginate(100);}
            else{
                return Prestamo::with("cliente")
                ->with('user')
                ->with('inventario')
                ->with('logprestamos')
                ->whereHas('cliente', function ($query) use ($filter,$tipo) {
                    $query->where('tipocliente',$tipo);
                    $query->where(function ($query2) use($filter){
                        $query2->where('local', 'like', '%'.$filter.'%')
                        ->orWhere('titular', 'like', '%'.$filter.'%');});
                    })
                ->where('estado',$estado)
                ->orderBy('id','desc')->paginate(100);}
        }
    }
    public function reportecliente(){
        return DB::SELECT('select local,titular,i.id,i.nombre,sum(p.prestado) as total,tipocliente,sum(p.efectivo) as monto from prestamos p inner join clientes c on p.cliente_id=c.id inner join inventarios i on p.inventario_id = i.id where p.prestado>0 and p.estado!="ANULADO" group by local,titular,i.id,i.nombre,tipocliente');
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
//        return $request;
         $inv=Inventario::find($request->inventario_id);
         if( $inv->cantidad>=$request->cantidad){
        $prestamo=new Prestamo();
        $prestamo->fecha=$request->fecha;
        $prestamo->estado=$request->tipo;
        if ($request->tipo=='VENTA'){
            $prestamo->cantidad=$request->cantidad;
            $prestamo->prestado=0;
//            $inv->cantidad=$inv->cantidad-$request->cantidad;
//            $inv->save();
        }else{
            $prestamo->cantidad=$request->cantidad;
            $prestamo->prestado=$request->cantidad;
        }
//        $prestamo->cantidad=$request->cantidad;
//        $prestamo->prestado=$request->cantidad;
        $prestamo->efectivo=$request->efectivo;
        $prestamo->fisico=strtoupper($request->fisico);
        $prestamo->observacion=strtoupper($request->observacion);
        $prestamo->user_id=$request->user()->id;
        $prestamo->cliente_id=$request->cliente_id;
        $prestamo->inventario_id=$request->inventario_id;
         $prestamo->save();

         if($request->tipo=='VENTA'){
            $general=General::find(1);
            $general->monto=$general->monto +  $prestamo->efectivo;
            $general->save();

            $loggeneral= new Loggeneral;
            $loggeneral->numero=$prestamo->id;
            $loggeneral->monto= $prestamo->efectivo;
            $loggeneral->detalle='PRESTAMO/VENTA';
            $loggeneral->motivo='VENTA INVENTARIO';
            $loggeneral->tipo='INGRESO';
            $loggeneral->fecha=$prestamo->fecha;
            $loggeneral->hora=date("H:i:s");
            $loggeneral->glosa_id=null;
            $loggeneral->user_id= $prestamo->user_id;
            $loggeneral->save();
         }
         $inv=Inventario::find($request->inventario_id);
         $inv->cantidad=$inv->cantidad - $request->cantidad;
         $inv->save();

         return  Prestamo::with('user')->with('cliente')->with('inventario')->where('id',$prestamo->id)->first();
        }
         else
            return response()->json(['error' => 'No se cuenta con la cantidad'], 404);
//        return $prestamo->
    }

    public function regprestamo(Request $request)
    {   //return $request->listado[0]['inventario_id'];
        $retorno =[];
        foreach ($request->listado as $value) {
            //return $value['inventario_id'];
            # code...
        $inv=Inventario::find($value['inventario_id']);
         if( $inv->cantidad>=$value['cantidad']){
        $prestamo=new Prestamo();
        $prestamo->fecha=$value['fecha'];
        $prestamo->estado=$value['tipo'];
        if ($value['tipo']=='VENTA'){
            $prestamo->cantidad=$value['cantidad'];
            $prestamo->prestado=0;
        }else{
            $prestamo->cantidad=$value['cantidad'];
            $prestamo->prestado=$value['cantidad'];
        }
        $prestamo->efectivo=$value['efectivo'];
        $prestamo->fisico=strtoupper($value['fisico']);
        $prestamo->observacion=strtoupper($value['observacion']);
        $prestamo->user_id=$request->user()->id;
        $prestamo->cliente_id=$value['cliente_id'];
        $prestamo->inventario_id=$inv->id;
         $prestamo->save();

         if($value['tipo']=='VENTA'){
            $general=General::find(1);
            $general->monto=$general->monto +  $prestamo->efectivo;
            $general->save();

            $loggeneral= new Loggeneral;
            $loggeneral->numero=$prestamo->id;
            $loggeneral->monto= $prestamo->efectivo;
            $loggeneral->detalle='PRESTAMO/VENTA';
            $loggeneral->motivo='VENTA INVENTARIO';
            $loggeneral->tipo='INGRESO';
            $loggeneral->fecha=$prestamo->fecha;
            $loggeneral->hora=date("H:i:s");
            $loggeneral->glosa_id=null;
            $loggeneral->user_id= $prestamo->user_id;
            $loggeneral->save();
         }
         $inv->cantidad=$inv->cantidad - $value['cantidad'];
         $inv->save();
         array_push($retorno, Prestamo::with('user')->with('cliente')->with('inventario')->where('id',$prestamo->id)->first());
        }

        }
        return $retorno;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Prestamo  $prestamo
     * @return \Illuminate\Http\Response
     */
    public function anularprestamo(Request $request){
        $prestamo=Prestamo::find($request->id);

        //$prestamo->user_id=$request->user()->id;
//            $prestamo->fecha=date('Y-m-d');
            $prestamo->fechaAnulacion=date('Y-m-d');
            $prestamo->motivoAnulacion=strtoupper($request->motivo);
            $prestamo->estado='ANULADO';

        if($prestamo->efectivo>0 && $prestamo->efectivo!=null && $prestamo->tipo!='VENTA'){
            $general=General::find(1);
            $general->monto=$general->monto + $prestamo->efectivo;
            $general->save();

            $loggeneral= new Loggeneral;
            $loggeneral->numero=$prestamo->id;
            $loggeneral->monto= $prestamo->efectivo;
            $loggeneral->detalle='PRESTAMO/ANULADO';
            $loggeneral->motivo='PRESTAMO INVENTARIO';
            $loggeneral->tipo='INGRESO';
            $loggeneral->fecha=$prestamo->fecha;
            $loggeneral->hora=date("H:i:s");
            $loggeneral->glosa_id=null;
            $loggeneral->user_id= $request->user()->id;
            $loggeneral->save();
        }
        if($request->tipo=='VENTA'){
            $general=General::find(1);
            $general->monto=$general->monto -  $prestamo->efectivo;
            $general->save();

            $loggeneral= new Loggeneral;
            $loggeneral->numero=$prestamo->id;
            $loggeneral->monto= $prestamo->efectivo;
            $loggeneral->detalle='PRESTAMO/VENTA/ANULADO';
            $loggeneral->motivo='VENTA INVENTARIO';
            $loggeneral->tipo='RETIRAR';
            $loggeneral->fecha=$prestamo->fecha;
            $loggeneral->hora=date("H:i:s");
            $loggeneral->glosa_id=null;
            $loggeneral->user_id= $prestamo->user_id;
            $loggeneral->save();
         }
        return $prestamo->save();
    }

public function reporteventa(Request $request){
    //if($request->user()->id==1)
    //{
        if($request->id==0)
        return Prestamo::with('cliente')->with('inventario')->
        whereDate('fecha','>=',$request->fecha1)
        ->whereDate('fecha','<=',$request->fecha2)
        ->where('estado','VENTA')
        ->get();
        else
        return Prestamo::with('cliente')->with('inventario')->
        whereDate('fecha','>=',$request->fecha1)
        ->whereDate('fecha','<=',$request->fecha2)
        ->where('estado','VENTA')
        ->where('user_id',$request->id)
        ->get();
    /*}
    else


        return Prestamo::with('cliente')->with('inventario')->
        whereDate('fecha','>=',$request->fecha1)
        ->whereDate('fecha','<=',$request->fecha2)
        ->where('estado','VENTA')
        ->where('user_id',$request->user()->id)
        ->get();*/
    }

    public function show(Prestamo $prestamo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Prestamo  $prestamo
     * @return \Illuminate\Http\Response
     */
    public function edit(Prestamo $prestamo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prestamo  $prestamo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Prestamo $prestamo)
    {
       // $prestamo->update($request->all());

        $inv=Inventario::find($request->inventario_id);
        if( $inv->cantidad>=$request->cantidad){

       $prestamo=Prestamo::find($request->id);
            if(Logprestamo::where('prestamo_id',$prestamo->id)->get()->count()>0){
                $inv=Inventario::find($prestamo->inventario_id);
                $inv->cantidad=$inv->cantidad + $prestamo->cantidad;
                $inv->save();
                $prestamo->cliente_id=$request->cliente_id;
                $prestamo->fecha=$request->fecha;
                $prestamo->cantidad=$request->cantidad;
                $prestamo->prestado=$request->cantidad;
                $prestamo->inventario_id=$request->inventario_id;
                $inv2=Inventario::find($request->inventario_id);
                $inv2->cantidad=$inv->cantidad - $request->cantidad;
                $inv2->save();
        }
       $prestamo->efectivo=$request->efectivo;
       $prestamo->fisico=strtoupper($request->fisico);
       $prestamo->observacion=strtoupper($request->observacion);
        $prestamo->save();


        return true;}
        else
           return response()->json(['error' => 'No se cuenta con la cantidad'], 404);
    }

    public function modprestamo(Request $request)
    {
       // $prestamo->update($request->all());

        $inv=Inventario::find($request->inventario_id);
        if( $inv->cantidad>=$request->cantidad){

       $prestamo=Prestamo::find($request->id);
            if(Logprestamo::where('prestamo_id',$prestamo->id)->count()==0){
                $inv=Inventario::find($prestamo->inventario_id);
                $inv->cantidad=$inv->cantidad + $prestamo->cantidad;
                $inv->save();
                $prestamo->cliente_id=$request->cliente_id;
                $prestamo->cantidad=$request->cantidad;
                $prestamo->prestado=$request->cantidad;
                $prestamo->inventario_id=$request->inventario_id;
                $inv2=Inventario::find($request->inventario_id);
                $inv2->cantidad=$inv->cantidad - $request->cantidad;
                $inv2->save();
        }
       $prestamo->efectivo=$request->efectivo;
       $prestamo->fisico=$request->fisico;
       $prestamo->observacion=$request->observacion;
        $prestamo->save();


        return true;}
        else
           return response()->json(['error' => 'No se cuenta con la cantidad'], 404);

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Prestamo  $prestamo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        return $prestamo;
        $prestamo=Prestamo::find($id);
        if(Logprestamo::where('prestamo_id',$prestamo->id)->count()>0)
            return false;
        if($prestamo->estado=='VENTA'){
            $general=General::find(1);
            $general->monto=$general->monto -  $prestamo->efectivo;
            $general->save();

            $loggeneral= Loggeneral::where('numero',$prestamo->id)
            ->where('detalle','PRESTAMO/VENTA')
            ->where('motivo','VENTA INVENTARIO')
            ->where('tipo','INGRESO')->first();
            $loggeneral->delete();
         }
        $inventario=Inventario::find($prestamo->inventario_id);
        $inventario->cantidad=$inventario->cantidad + $prestamo->cantidad;
        $inventario->save();
        $prestamo->delete();
    }

    public function misanulados(Request $request){
        //if($request->user()->id==1)
        //{
            if($request->id==0){
            return Prestamo::with('cliente')->with('inventario')
            ->where('estado','ANULADO')
            ->where('efectivo','>',0)
            ->whereDate('updated_at','>=',$request->fecha1)
            ->whereDate('updated_at','<=',$request->fecha2)
            ->get();}
            else{
            return Prestamo::with('cliente')->with('inventario')
            ->where('estado','ANULADO')
            ->where('efectivo','>',0)
            ->where('user_id',$request->id)
            ->whereDate('updated_at','>=',$request->fecha1)
            ->whereDate('updated_at','<=',$request->fecha2)
            ->get();
        }
        /*else
        return Prestamo::with('cliente')->with('inventario')
        ->where('estado','ANULADO')
        ->where('efectivo','>',0)
        ->where('user_id',$request->user()->id)
        ->whereDate('updated_at','>=',$request->fecha1)
        ->whereDate('updated_at','<=',$request->fecha2)
        ->get();
*/
        /*return Prestamo::with('cliente')

            ->where('estado','ANULADO')
            ->where('efectivo','>',0)
            ->whereDate('updated_at','>=',$request->fecha1)
            ->whereDate('updated_at','<=',$request->fecha2)
            ->get();*/
    }

    public function impresion($id){
        $garantia=Prestamo::with('user')->with('cliente')->with('inventario')->where('id',$id)->get()[0];
        $cadena='
        <style>
        .textc{text-align:center}
        .texttitulo{text-align:center; }
        .leyenda{text-align:justify;
        size-font 6px;}
        </style>
        <div style="padding:5px">
        <div class="texttitulo">Prestamo / Venta Material</div>
        <table style="width: 100%;">
        <tr><td>N </td><td>'.$garantia->id.'</td></tr>
        <tr><td>Fecha: </td><td>'.date('d/m/Y',strtotime($garantia->fecha)).'</td></tr>
        <tr><td>Nombre:</td><td> '.$garantia->cliente->titular.'</td></tr>
        <tr><td>Efectivo: </td><td>'.$garantia->efectivo.'</td></tr>
        <tr><td>Fisico: </td><td>'.$garantia->fisico.'</td></tr>
        <tr><td>Detalle: </td><td>'.$garantia->inventario->nombre.'</td></tr>
        <tr><td>Cantidad: </td><td>'.$garantia->cantidad.'</td></tr>
        <tr><td>Usuario: </td><td>'.$garantia->user->name.'</td></tr>
        </table>
        <hr style=" border: 4px dashed;">
        <div><b>FECHA </b>'.date('d/m/Y',strtotime($garantia->fecha)).'</div>
        <div><b>NOMBRE </b>'.$garantia->cliente->titular.'</div>
        <div><b>DETALLE </b> '.$garantia->cantidad.' - '.$garantia->inventario->nombre.'</div>
        <br>
        <br>
        <br>
        <div class="textc">Firma</div>';
        if($garantia->estado!='VENTA'){
        $cadena.='<br>
        <br>
        <div class="leyenda"><b>*  Accepto todas las condiciones y terminos de prestamo de envases </b></div>
            </div>
        ';}
        return $cadena;

    }

    public function impresionRep($id){
        $garantia=Prestamo::with('user')->with('cliente')->with('inventario')->where('id',$id)->get()[0];
        $cadena='
        <style>
        .textc{text-align:center; }
        .texttitulo{text-align:center; font-size:14px;font-weight: bold;}
        .leyenda{text-align:justify;
        size-font 6px;}
        </style>
        <div style="padding:5px">
        <div class="texttitulo">Prestamo / Venta Material</div>
        <table style="width: 100%;">
        <tr><td>N </td><td>'.$garantia->id.'</td></tr>
        <tr><td>Fecha: </td><td>'.date('d/m/Y',strtotime($garantia->fecha)).'</td></tr>
        <tr><td>Nombre:</td><td> '.$garantia->cliente->titular.'</td></tr>
        <tr><td>Efectivo: </td><td>'.$garantia->efectivo.'</td></tr>
        <tr><td>Fisico: </td><td>'.$garantia->fisico.'</td></tr>
        <tr><td>Detalle: </td><td>'.$garantia->inventario->nombre.'</td></tr>
        <tr><td>Cantidad: </td><td>'.$garantia->cantidad.'</td></tr>
        <tr><td>Usuario: </td><td>'.$garantia->user->name.'</td></tr>
        </table>
        <br>
        <br>
        <br>
        <div class="textc">Firma</div>';
        if($garantia->estado!='VENTA'){
        $cadena.='<br>
        <br>
        <div class="leyenda"><b>*  Accepto todas las condiciones y terminos de prestamo de envases </b></div>
            </div>
        ';}
        return $cadena;

    }

    public function tefectivo(){
        return DB::SELECT('SELECT SUM(efectivo) as total from prestamos where estado="EN PRESTAMO"');
    }
}
