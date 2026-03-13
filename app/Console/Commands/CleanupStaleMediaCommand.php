<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\StaleMediaCleanupService;
use Illuminate\Console\Command;

class CleanupStaleMediaCommand extends Command
{
    protected $signature = 'media:cleanup-stale';

    protected $description = 'Delete stale temporary media files and clear pending references.';

    public function handle(StaleMediaCleanupService $cleanupService): int
    {
        $deletedCount = $cleanupService->cleanup();

        $this->info("Deleted {$deletedCount} stale media artifact(s).");

        return self::SUCCESS;
    }
}
