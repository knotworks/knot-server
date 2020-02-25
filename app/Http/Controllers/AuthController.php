<?php

namespace Knot\Http\Controllers;

use Knot\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $token;
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
            'password' => 'required|confirmed',
        ]);

        return User::create($request->all());
    }

    public function logout()
    {
        auth()->logout();

        return response([], 204);
    }
}
