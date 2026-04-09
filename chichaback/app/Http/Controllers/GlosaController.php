<?php

namespace App\Http\Controllers;

use App\Models\Glosa;
use Illuminate\Http\Request;

class GlosaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Glosa::orderBy('orden','asc')->whereNotIn('id',[2,15])->get();
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
        $glosa=new Glosa;
        $glosa->nombre=strtoupper($request->nombre);
        $glosa->orden=$request->orden;
        $glosa->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Glosa  $glosa
     * @return \Illuminate\Http\Response
     */
    public function show(Glosa $glosa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Glosa  $glosa
     * @return \Illuminate\Http\Response
     */
    public function edit(Glosa $glosa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Glosa  $glosa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Glosa $glosa)
    {
        //
        $glosa=Glosa::find($request->id);
        $glosa->nombre=strtoupper($request->nombre);
        $glosa->orden=$request->orden;
        $glosa->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Glosa  $glosa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $glosa=Glosa::find($id);
        $glosa->delete();

    }
}
