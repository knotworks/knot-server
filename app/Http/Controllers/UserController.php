<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Knot\Models\User;

class UserController extends Controller
{
    /**
     * Return the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
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
    public function store(Request $request)
    {
        if (config('app.disable_new_signups')) {
            return response('Signups have been disabled.', 403);
        }

        $user = $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        return User::create($user);
    }
}
