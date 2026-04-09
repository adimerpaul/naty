<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Material;
use App\Models\General;
use App\Models\Loggeneral;
use App\Models\Logcompra;
use App\Models\Recuento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function observacionAdmin(Request $request){
        $compra=Compra::find($request->id);
        $compra->observacionAdmin=$request->observacionAdmin;
        $compra->save();
    }
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
        //        return $request;
        foreach ($request->compras as $r) {
            $material=Material::find($r['material_id']);
            $material->stock=$material->stock + $r['cantidad'];
            $material->save();
        $compra=new Compra;
        $compra->fecha=date('Y-m-d');
        $compra->hora=date('H:i:s');
        $compra->cantidad=$r['cantidad'];
        $compra->costo=$r['costo'];
        $compra->subtotal=$r['subtotal'];
        $compra->deuda=0;
        $compra->fechaven=$r['fechaven'];
        $compra->comentario=$r['comentario'];
        $compra->observacion=$r['observacion'];
        $compra->lote=$r['lote'];
        $compra->material_id=$r['material_id'];
        $compra->provider_id=$request->provider_id;
        $compra->user_id=$request->user_id;
        $compra->estado='POR PAGAR';
        $compra->save();

        /*$general=General::find(1);
        $general->monto=$general->monto - $compra->subtotal;
        $general->save();

        $loggeneral= new Loggeneral;
        $loggeneral->numero=$compra->id;
        $loggeneral->monto=$compra->subtotal;
        $loggeneral->detalle='COMPRA ALMACEN';
        $loggeneral->motivo='';
        $loggeneral->tipo='EGRESO';
        $loggeneral->fecha=$compra->fecha;
        $loggeneral->hora=date("H:i:s");
        $loggeneral->glosa_id=null;
        $loggeneral->user_id= $compra->user_id;
        $loggeneral->save();*/

        }

    }
    public function compra2(Request $request)
    {
        try {
            DB::beginTransaction();
            //        return $request;
            foreach ($request->compras as $r) {
                error_log(json_encode($r));
                $material=Material::find($r['material_id']);
                $material->stock=$material->stock + $r['cantidad'];
                $material->save();
                $compra=new Compra;
                $compra->fecha=date('Y-m-d');
                $compra->hora=date('H:i:s');
                $compra->cantidad=$r['cantidad'];
                $compra->costo=$r['costo']==null?0:$r['costo'];
                $compra->subtotal=$r['subtotal']==null?0:$r['subtotal'];
                $compra->deuda=0;
                $compra->fechaven=$r['fechaven'];
                $compra->comentario=isset($r['comentario'])?$r['comentario']:'';
                $compra->observacion=$r['observacion'];
                $compra->lote=$r['lote'];
                $compra->material_id=$r['material_id'];
                $compra->provider_id=isset($r['provider_id'])?$r['provider_id']:$request->provider_id;
                $compra->user_id=$request->user_id;
                $compra->estado='POR PAGAR';
                $compra->save();

                /*$general=General::find(1);
                $general->monto=$general->monto - $compra->subtotal;
                $general->save();

                $loggeneral= new Loggeneral;
                $loggeneral->numero=$compra->id;
                $loggeneral->monto=$compra->subtotal;
                $loggeneral->detalle='COMPRA ALMACEN';
                $loggeneral->motivo='';
                $loggeneral->tipo='EGRESO';
                $loggeneral->fecha=$compra->fecha;
                $loggeneral->hora=date("H:i:s");
                $loggeneral->glosa_id=null;
                $loggeneral->user_id= $compra->user_id;
                $loggeneral->save();*/

            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function consultar(Request $request)
    {
        //
        return Compra::with('material')->with('provider')
        ->with(['logcompras' => function ($query) { $query->orderBy('id', 'desc');}])
        ->with(['recuentos' => function ($query) { $query->orderBy('id', 'desc');}])
        ->where('material_id',$request->material_id)
        ->whereDate('fecha','>=',$request->fecha1)
        ->where('fecha','<=',$request->fecha2)->get();
    }
    public function consultar2(Request $request)
    {
        //
        return Compra::with('material')
            ->with('provider')
            ->with(['recuentos' => function ($query) {$query->orderBy('id', 'desc');}])
            ->with(['logcompras'=> function ($q) {$q->orderBy('id','desc');}])
//            ->where('material_id',$request->material_id)
            ->whereDate('fecha','>=',$request->fecha1)
            ->where('fecha','<=',$request->fecha2)
            ->orderBy('id','desc')
            ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function edit(Compra $compra)
    {
        //
    }
    public function compraModificar(Request $request){

        $compra=Compra::find($request->id);
        if($compra->cantidad <> $request->cantidad){
        $material=Material::find($request->material_id);
        $material->stock=$material->stock - $compra->cantidad + $request->cantidad;
        $material->save();
    }

        $compra->cantidad=$request->cantidad;
        $compra->costo=$request->costo;
        $compra->subtotal=$request->costo*$request->cantidad;
        $compra->fechaven=$request->fechaven;
        $compra->comentario=$request->comentario;
        $compra->observacion=$request->observacion;
        $compra->lote=$request->lote;
        $compra->material_id=$request->material_id;
        $compra->provider_id=$request->provider_id;
        $compra->user_id=$request->user()->id;
        $compra->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Compra $compra)
    {
        //
        $compra=Compra::find($request->id);
        $material=Material::find($request->material_id);
        if($compra->cantidad != $request->cantidad){
            $material->stock=$material->stock - $compra->cantidad + $request->cantidad;
            $material->save();
        }
        /*$general=General::find(1);
        $general->monto=$general->monto +  $compra->subtotal - $request->subtotal;
        $general->save();

        $loggeneral= Loggeneral::where('numero',$compra->id)->where('detalle','COMPRA ALMACEN')->where('tipo','EGRESO')->get()[0];
        $loggeneral->monto=$compra->subtotal;
        $loggeneral->glosa_id=null;
        $loggeneral->save();

        $compra->fechaven=$request->fechaven;
        $compra->cantidad=$request->cantidad;
        $compra->costo=$request->costo;
        $compra->subtotal=$request->subtotal;
        $compra->observacion=$request->observacion;
        $compra->lote=$request->lote;
        $compra->save();
*/


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Compra  $compra
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $compra=Compra::find($id);
        if((Recuento::where('compra_id',$id)->count())>0)
            return response()->json(['error' => 'Existe registro retiro'], 500);
        if((Logcompra::where('compra_id',$id)->count())>0)
            return response()->json(['error' => 'Existe registro pago'], 500);

        /*$general=General::find(1);
        $general->monto=$general->monto +  $compra->subtotal ;
        $general->save();

        $loggeneral= Loggeneral::where('numero',$compra->id)->where('detalle','COMPRA ALMACEN')->where('tipo','EGRESO')->first();
        $loggeneral->delete();
        */
        $material=Material::find($compra->material_id);
        $material->stock=$material->stock - $compra->cantidad;
        $material->save();
        $compra->delete();
    }


}
