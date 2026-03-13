<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function showPostMedia(Request $request, PostMedia $postMedia)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($postMedia->status === 'ready', 404);

        $this->authorize('can_view_post', $postMedia->post);

        $path = $request->boolean('poster') ? $postMedia->poster_path : $postMedia->path;
        abort_unless($path, 404);

        return Storage::disk($postMedia->disk)->download($path);
    }

    public function showAvatar(Request $request, User $user)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($user->avatar, 404);

        $this->authorize('can_view_profile', $user);

        return Storage::disk($user->avatar_disk ?: config('media.disk'))->download($user->avatar);
    }
}
