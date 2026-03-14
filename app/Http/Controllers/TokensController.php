<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Token Authentication
 *
 * Bearer-token authentication for API clients.
 */
class TokensController extends Controller
{
    /**
     * Create a personal access token.
     *
     * Exchange valid user credentials for a Sanctum bearer token.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The user's email address. Example: jane@example.com
     * @bodyParam password string required The user's password. Example: password
     * @bodyParam device_name string required A label for the client device or app. Example: iPhone 16
     *
     * @response 200 {"token":"1|abcdefghijklmnopqrstuvwxyz"}
     * @response 422 {"message":"The provided credentials are incorrect.","errors":{"email":["The provided credentials are incorrect."]}}
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::whereEmail($credentials['email'])->firstOrFail();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($credentials['device_name']);

        return ['token' => $token->plainTextToken];
    }
}
