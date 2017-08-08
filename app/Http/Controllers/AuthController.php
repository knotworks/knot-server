<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Knot\Models\User;
use Doorman;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => [
            'user',
        ]]);
    }

    /**
     * Return the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        return auth()->user();
    }

    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'code' => 'required|doorman:email',
        ]);
        try {
            Doorman::redeem($request->input('code'), $request->input('email'));

            return User::create($request->all());
        } catch (\DoormanException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
