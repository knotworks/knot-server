<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Throwable;

class MediaUrlFactory
{
    public function postMediaUrl(PostMedia $media): ?string
    {
        if ($media->status !== 'ready' || ! $media->path) {
            return null;
        }

        return $this->temporaryUrl(
            $media->disk,
            $media->path,
            'media.posts.show',
            ['postMedia' => $media->id],
        );
    }

    public function postPosterUrl(PostMedia $media): ?string
    {
        if ($media->status !== 'ready' || ! $media->poster_path) {
            return null;
        }

        return $this->temporaryUrl(
            $media->disk,
            $media->poster_path,
            'media.posts.show',
            ['postMedia' => $media->id, 'poster' => 1],
        );
    }

    public function avatarUrl(User $user): ?string
    {
        if (! $user->avatar) {
            return null;
        }

        return $this->temporaryUrl(
            $user->avatar_disk ?: config('media.disk'),
            $user->avatar,
            'media.avatars.show',
            ['user' => $user->id],
        );
    }

    /**
     * @param  array<string, int|string>  $routeParameters
     */
    private function temporaryUrl(string $disk, string $path, string $routeName, array $routeParameters): string
    {
        $expiresAt = Date::now()->addMinutes(config('media.url_ttl_minutes'));

        try {
            return Storage::disk($disk)->temporaryUrl($path, $expiresAt);
        } catch (Throwable) {
            return URL::temporarySignedRoute($routeName, $expiresAt, $routeParameters);
        }
    }
}
