<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Users
 *
 * Endpoints for registration and retrieving the authenticated user.
 */
class UserController extends Controller
{
    /**
     * Fetch the authenticated user.
     *
     * @authenticated
     *
     * @response 200 {"id":1,"first_name":"Jane","last_name":"Doe","email":"jane@example.com"}
     */
    public function show()
    {
        return auth()->user();
    }

    /**
     * Register a new user.
     *
     * @unauthenticated
     *
     * @bodyParam first_name string required The user's first name. Example: Jane
     * @bodyParam last_name string The user's last name. Example: Doe
     * @bodyParam email string required A unique email address. Example: jane@example.com
     * @bodyParam password string required The desired password. Example: password
     * @bodyParam password_confirmation string required Must match `password`. Example: password
     *
     * @response 201 {"id":1,"first_name":"Jane","last_name":"Doe","email":"jane@example.com"}
     * @response 403 {"message":"New signups have been disabled."}
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
