<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use App\Jobs\ProcessAvatarUpload;
use App\Models\User;
use App\Services\MediaPathGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * @group Profile
 *
 * View and update user profiles.
 *
 * @authenticated
 */
class ProfileController extends Controller
{
    /**
     * Fetch a user's profile.
     *
     * @urlParam user integer required The user ID. Example: 1
     *
     * @response 200 {"user":{"id":1,"first_name":"Jane"},"posts":[]}
     * @response 403 {"message":"This action is unauthorized."}
     */
    public function show(User $user)
    {
        $this->authorize('can_view_profile', $user);

        return [
            'user' => $user,
            'posts' => $user->feed(),
        ];
    }

    /**
     * Upload a new avatar.
     *
     * @response 200 {"id":1,"avatar_status":"pending","avatar_temporary_path":"avatars/temp/avatar.jpg"}
     * @response 422 {"message":"The given data was invalid.","errors":{"avatar":["An avatar image is required."]}}
     */
    public function updateAvatar(UpdateAvatarRequest $request, MediaPathGenerator $mediaPathGenerator)
    {
        $user = auth()->user();
        $avatar = $request->file('avatar');
        $temporaryPath = $mediaPathGenerator->temporaryAvatarPath($user, $avatar);

        $avatar->storeAs(
            dirname($temporaryPath),
            basename($temporaryPath),
            config('media.temporary_disk'),
        );

        $user->forceFill([
            'avatar_status' => 'pending',
            'avatar_disk' => config('media.disk'),
            'avatar_temporary_path' => $temporaryPath,
            'avatar_mime_type' => $avatar->getMimeType(),
            'avatar_bytes' => $avatar->getSize(),
            'avatar_failure_code' => null,
            'avatar_failure_message' => null,
            'avatar_processing_started_at' => null,
            'avatar_processed_at' => null,
            'avatar_failed_at' => null,
        ])->save();

        ProcessAvatarUpload::dispatch($user->id)->afterCommit();

        return $user->fresh();
    }

    /**
     * Update the authenticated user's profile details.
     *
     * @bodyParam first_name string required The user's first name. Example: Jane
     * @bodyParam last_name string The user's last name. Example: Doe
     * @bodyParam email string required The user's email address. Example: jane@example.com
     * @bodyParam current_password string Required when updating the password. Example: old-password
     * @bodyParam password string The new password. Example: new-password
     * @bodyParam password_confirmation string Must match `password` when supplied. Example: new-password
     *
     * @response 200 {"id":1,"first_name":"Jane","last_name":"Doe","email":"jane@example.com"}
     * @response 422 {"error":"The provided current password did not match our records."}
     */
    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|confirmed',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email'));

        if ($request->filled('current_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->update(['password' => $request->password]);
            } else {
                return response()->json(['error' => 'The provided current password did not match our records.'], 422);
            }
        }

        return $user;
    }
}
