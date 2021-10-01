<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller
{

    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message' => 'Usuario o contraseña incorrectos'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'name' => $user['name'],
            'lastname' => $user['lastname'],
            'email' => $user['email'],
            'permissions' => $user['permissions'],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);

    }

    public function logout(Request $request)
    {
        try {
            $user = User::find($request->user()->id);
            $user->tokens()->delete();
            return response()->json([
                'message' => 'OK'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 400);
        }
    }

}
