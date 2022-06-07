<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Hash;
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

    public function register(Request $request) {
        try {
            $input = $request->only('name', 'username', 'email', 'password');

            $validator = Validator::make($input, [
                'name' => 'required',
                'username' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            if($validator->fails()){
                return sendError('Something went wrong!', $validator->errors());

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!',
                    'data' => $validator->errors()
                ], 404);
            }

            $input['password'] = Hash::make($input['password']);

            User::create($input);

            return response()->json([
                'success' => true,
                'message' => 'Registration success'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'data' => $e->getMessage()
            ], 404);
        }
    }

    public function user()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'success get user data'
        ], 200);
    }
}
