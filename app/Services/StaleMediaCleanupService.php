<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class StaleMediaCleanupService
{
    public function cleanup(): int
    {
        $deleted = 0;
        $disk = Storage::disk(config('media.temporary_disk'));
        $cutoff = Date::now()->subHours(config('media.cleanup_after_hours'));

        $stalePostMedia = PostMedia::query()
            ->whereNotNull('temporary_path')
            ->whereIn('status', ['pending', 'failed'])
            ->where('updated_at', '<', $cutoff)
            ->get();

        foreach ($stalePostMedia as $media) {
            if ($media->temporary_path) {
                $disk->delete($media->temporary_path);
            }

            $media->forceFill(['temporary_path' => null])->save();
            $deleted++;
        }

        $staleAvatars = User::query()
            ->whereNotNull('avatar_temporary_path')
            ->whereIn('avatar_status', ['pending', 'failed'])
            ->where('updated_at', '<', $cutoff)
            ->get();

        foreach ($staleAvatars as $user) {
            if ($user->avatar_temporary_path) {
                $disk->delete($user->avatar_temporary_path);
            }

            $user->forceFill(['avatar_temporary_path' => null])->save();
            $deleted++;
        }

        return $deleted;
    }
}
