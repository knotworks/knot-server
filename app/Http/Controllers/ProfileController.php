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
     *
     * @return Response
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
