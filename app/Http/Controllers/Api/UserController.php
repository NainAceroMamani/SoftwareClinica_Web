<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use JwtAuth;

class UserController extends Controller
{
    public function show(){
        // info del usuario que se autentifico a traves de nuestra api
        return Auth::guard('api')->user();
    }

    public function update(Request $request) {
        $user = Auth::guard('api')->user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        JwtAuth:clearCache($user); // el paquete guarda en cache la info de usuario por eso cuando de vuelve la info una vez actualizada es antigua por eso borramos cache para que haga la petición de nuevo 
    }
}
