<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! auth()->attempt($credentials, true)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        $request->session()->regenerate();

        return response()->noContent();
    }

    public function destroy(Request $request)
    {
        auth()->user()->tokens()->delete();
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
