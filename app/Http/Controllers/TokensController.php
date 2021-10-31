<?php

namespace Knot\Http\Controllers;

use Knot\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TokensController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::whereEmail($credentials['email'])->firstOrFail();

        abort_if(!$user || !Hash::check($credentials['password'], $user->password), 401, 'Invalid credentials');

        $token = $user->createToken('Personal Access Token');

        return ['token' => $token->plainTextToken];
    }
}
