<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Return the authenticated user.
     *
     * @return Response
     */
    public function show()
    {
        return auth()->user();
    }

    /**
     * Register a new user.
     *
     *
     * @return Response
     */
    public function store(Request $request)
    {
        abort_if(config('app.disable_new_signups'), 403, 'New signups have been disabled.');

        $user = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        return User::create($user);
    }
}
