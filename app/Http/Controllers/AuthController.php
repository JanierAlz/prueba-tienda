<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'last_name' => $attr['last_name'],
            'email' => $attr['email'],
            'password' => bcrypt($attr['password'])
        ]);

        $token = $user->createToken('myAppToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $attr['email'])->first();
        if(!$user || !Hash::check($attr['password'], $user->password)){
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }
        
        $token = $user->createToken('myAppToken')->plainTextToken;

        $response = [
            'user' => 'logged in, welcome! use the issued token to access the api',
            'token' => $token,
        ];

        return response($response, 200);
    }


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function index()
    {
        return User::all();
    }
}
