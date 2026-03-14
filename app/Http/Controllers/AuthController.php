<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @group Session Authentication
 *
 * Session-based authentication endpoints for first-party clients.
 */
class AuthController extends Controller
{
    /**
     * Log in with an email address and password.
     *
     * Creates a session-backed authenticated state for clients using cookies instead of bearer tokens.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The user's email address. Example: jane@example.com
     * @bodyParam password string required The user's password. Example: password
     *
     * @response 204 {}
     * @response 401 {"error":"Invalid Credentials"}
     */
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

    /**
     * Log out the current session.
     *
     * Revokes the current user's API tokens and clears their authenticated web session.
     *
     * @authenticated
     *
     * @response 204 {}
     */
    public function destroy(Request $request)
    {
        auth()->user()->tokens()->delete();
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
