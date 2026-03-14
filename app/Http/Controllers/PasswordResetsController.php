<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @group Password Resets
 *
 * Password reset endpoints for account recovery.
 */
class PasswordResetsController extends Controller
{
    /**
     * Render the password reset page.
     *
     * Returns the HTML view used after a user clicks a password reset link.
     *
     * @unauthenticated
     *
     * @urlParam token string required The password reset token from the email link. Example: 6a0ef4e3816d9fb8
     *
     * @response 200 <html><body>Password reset page</body></html>
     */
    public function show(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Send a password reset link.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The email address for the account. Example: jane@example.com
     *
     * @response 204 {}
     * @response 422 {"message":"The email field must be a valid email address.","errors":{"email":["The email field must be a valid email address."]}}
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email'),
            ],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->noContent();
        }

        return response(['email' => $status], 500);
    }

    /**
     * Reset a password with a valid token.
     *
     * @unauthenticated
     *
     * @bodyParam token string required The password reset token from the email link. Example: 6a0ef4e3816d9fb8
     * @bodyParam email string required The email address for the account. Example: jane@example.com
     * @bodyParam password string required The new password. Example: new-password
     * @bodyParam password_confirmation string required Must match `password`. Example: new-password
     *
     * @response 200 Your password has been successfully reset. You may now go back to the app and sign in with your new password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                ? response('Your password has been successfully reset. You may now go back to the app and sign in with your new password.', 200)
                : back()->withErrors(['email' => trans($status)]);
    }
}
