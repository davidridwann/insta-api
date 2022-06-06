<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('InstaApp')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'data' => $user,
                'message' => 'login success'
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'unauthorized'
            ], 404);
        }
    }
}
