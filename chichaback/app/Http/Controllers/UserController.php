<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        return User::
            with('permisos')->get();
            //->where('id','!=',1)->get();
    }
    public function listuser(){
        return User::
//            ->with('permisos')
            where('estado','ACTIVO')
            ->get();
    }
    public function login(Request $request){
        if (!Auth::attempt($request->all())){
            return response()->json(['res'=>'No existe el usuario'],400);
        }
        if (User::where('email',$request->email)->where('estado','ACTIVO')->whereDate('fechalimite','>',now())->get()->count()==0){
            return response()->json(['res'=>'Su usuario sobre paso el limite de ingreso'],400);
        }

        $user=User::where('email',$request->email)
//            ->with('unid')
            ->with('permisos')
            ->firstOrFail();
        $log=new Log();
        $log->fecha=date('Y-m-d');
        $log->hora=date('H:i:s');
        $log->user_id=$user->id;
        $log->save();
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$user],200);
    }
    public function store(Request $request){
//        return ;
        $user=new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->fechalimite=$request->fechalimite;
        $user->password= Hash::make($request->password) ;
        $user->save();
        $permisos= array();
        foreach ($request->permisos as $permiso){
//            echo $permiso['estado'].'  ';
            if ($permiso['estado']==true)
                $permisos[]=$permiso['id'];
        }
        $permiso = Permiso::find($permisos);
        $user->permisos()->attach($permiso);

    }
    public function update(Request $request,User $user){
        $user->update($request->all());
        return $user;
    }
    public function updatepermisos(Request $request,User $user){
        $permisos= array();
        foreach ($request->permisos as $permiso){
            if ($permiso['estado']==true)
                $permisos[]=$permiso['id'];
        }
        $permiso = Permiso::find($permisos);
        $user->permisos()->detach();
        $user->permisos()->attach($permiso);
    }
    public function pass(Request $request,User $user){
//        return $request->password;
        $user->update([
            'password'=>Hash::make($request->password)
        ]);
        return $user;
    }
    public function destroy(User $user){
        $user->delete();
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['res'=>'salido exitosamente'],200);
    }
    public function me(Request $request){
//        $user=$request->user()->with('unid')->with('permisos')->firstOrFail();
//        $user=$request->user()
        $user=User::where('id',$request->user()->id)->where('estado','ACTIVO')
//            ->with('unid')
            ->with('permisos')
            ->firstOrFail();
        return $user;

//        return User::where('id',1)->with('unid')->get();
    }

    public function cambioestado(Request $request){
        $user=User::find($request->id);
        if($user->estado=='ACTIVO')
            $user->estado='INACTIVO';
        else
            $user->estado='ACTIVO';
        $user->save();

    }

    public function loginHistorial(Request $request){
        return Log::whereDate('fecha','>=',$request->ini)->whereDate('fecha','<=',$request->fin)->with('user')->get();
    }
}
