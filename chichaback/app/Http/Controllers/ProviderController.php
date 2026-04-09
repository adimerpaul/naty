<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Provider::all();
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
        $provider=new Provider;
        $provider->razon=strtoupper($request->razon);
        $provider->nombre=strtoupper($request->nombre);
        $provider->nit=$request->nit;
        $provider->direccion=$request->direccion;
        $provider->email=$request->email;
        $provider->telefono=$request->telefono;
        $provider->estado='ACTIVO';
        $provider->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Provider $provider)
    {
        //
        $provider=Provider::find($request->id);
        $provider->razon=strtoupper($request->razon);
        $provider->nombre=strtoupper($request->nombre);
        $provider->nit=$request->nit;
        $provider->direccion=$request->direccion;
        $provider->email=$request->email;
        $provider->telefono=$request->telefono;
        $provider->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $provider=Provider::find($id);
        $provider->delete();
    }
}
