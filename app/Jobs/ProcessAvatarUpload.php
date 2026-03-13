<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\MediaStatusUpdated;
use App\Models\User;
use App\Services\ImageProcessor;
use App\Services\MediaPathGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessAvatarUpload implements ShouldQueue
{
    use Queueable;

    public int $timeout = 180;

    public int $tries = 3;

    public function __construct(
        public int $userId,
    ) {}

    public function handle(
        ImageProcessor $imageProcessor,
        MediaPathGenerator $mediaPathGenerator,
    ): void {
        $user = User::query()->find($this->userId);

        if (! $user || ! $user->avatar_temporary_path) {
            return;
        }

        $temporaryDisk = Storage::disk(config('media.temporary_disk'));
        $finalDisk = Storage::disk($user->avatar_disk ?: config('media.disk'));
        $temporaryPath = $user->avatar_temporary_path;
        $sourcePath = $temporaryDisk->path($temporaryPath);
        $previousAvatar = $user->avatar;

        $user->forceFill([
            'avatar_status' => 'processing',
            'avatar_processing_started_at' => Date::now(),
            'avatar_failure_code' => null,
            'avatar_failure_message' => null,
            'avatar_failed_at' => null,
        ])->save();

        $processed = $imageProcessor->process($sourcePath);
        $finalPath = $mediaPathGenerator->finalAvatarPath($user, $processed['extension']);

        $finalDisk->put($finalPath, fopen($processed['path'], 'r'));

        $user->forceFill([
            'avatar' => $finalPath,
            'avatar_temporary_path' => null,
            'avatar_status' => 'ready',
            'avatar_mime_type' => $processed['mime_type'],
            'avatar_bytes' => $processed['bytes'],
            'avatar_failure_code' => null,
            'avatar_failure_message' => null,
            'avatar_processed_at' => Date::now(),
            'avatar_failed_at' => null,
        ])->save();

        $temporaryDisk->delete($temporaryPath);
        @unlink($processed['path']);

        if ($previousAvatar && $previousAvatar !== $finalPath) {
            DeleteRemoteMediaAssets::dispatch(
                $user->avatar_disk ?: config('media.disk'),
                [$previousAvatar],
                [],
            );
        }

        broadcast(new MediaStatusUpdated($user->id, [
            'resource_type' => 'avatar',
            'user_id' => $user->id,
            'status' => $user->avatar_status,
            'url' => $user->avatar_url,
            'failure_message' => $user->avatar_failure_message,
        ]));
    }

    public function failed(?Throwable $exception): void
    {
        $user = User::query()->find($this->userId);

        if (! $user) {
            return;
        }

        $user->forceFill([
            'avatar_status' => 'failed',
            'avatar_failure_code' => 'processing_failed',
            'avatar_failure_message' => 'We could not process this avatar upload.',
            'avatar_failed_at' => Date::now(),
        ])->save();

        if ($user->avatar_temporary_path) {
            Storage::disk(config('media.temporary_disk'))->delete($user->avatar_temporary_path);
        }

        broadcast(new MediaStatusUpdated($user->id, [
            'resource_type' => 'avatar',
            'user_id' => $user->id,
            'status' => $user->avatar_status,
            'url' => null,
            'failure_message' => $user->avatar_failure_message,
        ]));
    }
}
