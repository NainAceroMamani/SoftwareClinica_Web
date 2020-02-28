<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use JwtAuth;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // el if comparar si los datos son correctos la function attempt lo hace del paquete que instalamos con guard('api') llamamos al paquete
        if(Auth::guard('api')->attempt($credentials)) {
            $user = Auth::guard('api')->user();
            $jwt = JwtAuth::generateToken($user);
            $success = true;

            // Return successfull sign in response with the generated jwt.
            return compact('success', 'user', 'jwt');
        } else {
            $success = false;
            $message = 'Invalid credentials';

            // Return response for failed attempt...
            return compact('success', 'message');
        }
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        $success = true;
        return compact('success');
    }
}
