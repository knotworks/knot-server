<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Knot\Models\User;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $this->authorize('can_view_profile', $user);

        return [
            'user' => $user,
            'posts' => $user->feed(),
        ];
    }

    /**
     * Set the user's avatar.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request)
    {
        $avatar = $request->validate(['avatar' => 'required|string']);

        auth()->user()->update($avatar);

        return auth()->user();
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'current_password' => 'required_with:password',
            'password' => 'confirmed',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email'));

        if ($request->has('current_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->update(['password' => $request->password]);
            } else {
                return response()->json(['error' => 'The provided current password did not match our records.'], 422);
            }
        }

        return $user;
    }
}
