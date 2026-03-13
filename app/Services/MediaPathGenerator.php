<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaPathGenerator
{
    public function temporaryPostMediaPath(PostMedia $media, UploadedFile $file): string
    {
        return $this->prefix("tmp/posts/{$media->post_id}/{$media->id}-" . Str::uuid() . '.' . $this->extension($file));
    }

    public function finalPostMediaPath(PostMedia $media, string $extension): string
    {
        return $this->prefix("posts/{$media->post_id}/{$media->id}.{$extension}");
    }

    public function finalPostPosterPath(PostMedia $media): string
    {
        return $this->prefix("posts/{$media->post_id}/{$media->id}-poster.jpg");
    }

    public function temporaryAvatarPath(User $user, UploadedFile $file): string
    {
        return $this->prefix("tmp/avatars/{$user->id}/" . Str::uuid() . '.' . $this->extension($file));
    }

    public function finalAvatarPath(User $user, string $extension): string
    {
        return $this->prefix("avatars/{$user->id}/avatar.{$extension}");
    }

    private function prefix(string $path): string
    {
        return app()->environment() . '/' . ltrim($path, '/');
    }

    private function extension(UploadedFile $file): string
    {
        return strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
    }
}
