<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class DeleteRemoteMediaAssets implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $disk,
        public array $paths,
        public array $temporaryPaths,
    ) {}

    public function handle(): void
    {
        $paths = array_values(array_filter($this->paths));
        $temporaryPaths = array_values(array_filter($this->temporaryPaths));

        if ($paths !== []) {
            Storage::disk($this->disk)->delete($paths);
        }

        if ($temporaryPaths !== []) {
            Storage::disk(config('media.temporary_disk'))->delete($temporaryPaths);
        }
    }
}
