<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\MediaStatusUpdated;
use App\Models\PostMedia;
use App\Services\ImageProcessor;
use App\Services\MediaPathGenerator;
use App\Services\VideoProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessPostMedia implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public int $tries = 3;

    public function __construct(
        public int $postMediaId,
    ) {}

    public function handle(
        ImageProcessor $imageProcessor,
        MediaPathGenerator $mediaPathGenerator,
        VideoProcessor $videoProcessor,
    ): void {
        $media = PostMedia::query()->with('post.user')->find($this->postMediaId);

        if (! $media || ! $media->temporary_path) {
            return;
        }

        $temporaryDisk = Storage::disk(config('media.temporary_disk'));
        $finalDisk = Storage::disk($media->disk);
        $temporaryPath = $media->temporary_path;
        $sourcePath = $temporaryDisk->path($temporaryPath);

        $media->forceFill([
            'status' => 'processing',
            'processing_started_at' => Date::now(),
            'failed_at' => null,
            'failure_code' => null,
            'failure_message' => null,
        ])->save();

        $processed = $media->type === 'video'
            ? $videoProcessor->process($sourcePath)
            : $imageProcessor->process($sourcePath);

        $finalPath = $mediaPathGenerator->finalPostMediaPath($media, $processed['extension']);
        $finalDisk->put($finalPath, fopen($processed['path'], 'r'));

        $posterPath = null;
        if ($media->type === 'video' && isset($processed['poster_path'])) {
            $posterPath = $mediaPathGenerator->finalPostPosterPath($media);
            $finalDisk->put($posterPath, fopen($processed['poster_path'], 'r'));
        }

        $media->forceFill([
            'path' => $finalPath,
            'poster_path' => $posterPath,
            'temporary_path' => null,
            'status' => 'ready',
            'failure_code' => null,
            'failure_message' => null,
            'mime_type' => $processed['mime_type'],
            'bytes' => $processed['bytes'],
            'width' => $processed['width'],
            'height' => $processed['height'],
            'duration_seconds' => $processed['duration_seconds'] ?? null,
            'processed_at' => Date::now(),
            'failed_at' => null,
        ])->save();

        $temporaryDisk->delete($temporaryPath);
        @unlink($processed['path']);

        if (isset($processed['poster_path'])) {
            @unlink($processed['poster_path']);
        }

        broadcast(new MediaStatusUpdated($media->post->user_id, [
            'resource_type' => 'post_media',
            'post_id' => $media->post_id,
            'media_id' => $media->id,
            'status' => $media->status,
            'url' => $media->url,
            'poster_url' => $media->poster_url,
            'failure_message' => $media->failure_message,
        ]));
    }

    public function failed(?Throwable $exception): void
    {
        $media = PostMedia::query()->with('post.user')->find($this->postMediaId);

        if (! $media) {
            return;
        }

        $media->forceFill([
            'status' => 'failed',
            'failure_code' => 'processing_failed',
            'failure_message' => 'We could not process this upload.',
            'failed_at' => Date::now(),
        ])->save();

        if ($media->temporary_path) {
            Storage::disk(config('media.temporary_disk'))->delete($media->temporary_path);
        }

        broadcast(new MediaStatusUpdated($media->post->user_id, [
            'resource_type' => 'post_media',
            'post_id' => $media->post_id,
            'media_id' => $media->id,
            'status' => $media->status,
            'url' => null,
            'poster_url' => null,
            'failure_message' => $media->failure_message,
        ]));
    }
}
