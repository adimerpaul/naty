<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Permiso::orderBy('grupo')->get();
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
    public function orden(Request $request){
        $permiso = Permiso::find($request->permiso_id);
        error_log("permiso: " . json_encode($permiso));
        if ($request->accion == 'Aumentar') {
            $permisoArriba = Permiso::whereOrden($permiso->orden - 1)->first();
            error_log("permisoArriba: " . json_encode($permisoArriba));
            if ($permisoArriba) {
                $permisoArriba->orden = $permisoArriba->orden + 1;
                $permisoArriba->save();
                $permiso->orden = $permiso->orden - 1;
                $permiso->save();
            }
        } else if ($request->accion == 'Disminuir') {
            $permisoAbajo = Permiso::whereOrden($permiso->orden + 1)->first();
            if ($permisoAbajo) {
                $permisoAbajo->orden = $permisoAbajo->orden - 1;
                $permisoAbajo->save();
                $permiso->orden = $permiso->orden + 1;
                $permiso->save();
            }
        }
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permiso  $permiso
     * @return \Illuminate\Http\Response
     */
    public function show(Permiso $permiso)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permiso  $permiso
     * @return \Illuminate\Http\Response
     */
    public function edit(Permiso $permiso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permiso  $permiso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permiso $permiso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permiso  $permiso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permiso $permiso)
    {
        //
    }
}
