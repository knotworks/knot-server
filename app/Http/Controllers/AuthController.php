<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Knot\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        return response()->json('', 204);
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
        if (config('app.disable_new_signups')) {
            return response('Signups have been disabled.', 401);
        }

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
