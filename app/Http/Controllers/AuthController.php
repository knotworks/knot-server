<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use FamJam\Models\User;
use Doorman;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => [
            'user'
        ]]);
    }
    
    public function user()
    {
        return auth()->user();
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'code' => 'required|doorman:email',
        ]);
        try {
            Doorman::redeem($request->input('code'), $request->input('email'));
            
            return User::create($request->only([
                'name', 
                'email', 
                'password',
            ]));
        } catch (\DoormanException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
