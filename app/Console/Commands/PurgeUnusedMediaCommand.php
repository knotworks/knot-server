<?php

namespace Knot\Console\Commands;

use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Cloudinary;
use Illuminate\Console\Command;
use Knot\Models\PostMedia;
use Knot\Models\User;

class PurgeUnusedMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges unused media files from Cloudinary';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $env = app()->environment();

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.key'),
                'api_secret' => config('services.cloudinary.secret'),
            ],
        ]);

        $api = new AdminApi($cloudinary->configuration);

        $existingPaths = collect(PostMedia::all()->map->path->concat(User::all()->map->avatar));

        $res = (array) $api->assets(['prefix' => $env, 'type' => 'upload', 'max_results' => 500]);

        $assets = collect($res['resources']);

        while (array_key_exists('next_cursor', $res)) {
            $res = (array) $api->assets(['prefix' => $env, 'type' => 'upload', 'max_results' => 500, 'next_cursor' => $res['next_cursor']]);
            $assets = $assets->concat($res['resources']);
        }

        $idsToDelete = $assets->reject(function ($value) use ($existingPaths) {
            return $existingPaths->contains($value['public_id']);
        })->map->public_id->toArray();

        $idCount = count($idsToDelete);

        if ($idCount > 0) {
            $this->info("Found {$idCount} photo(s) to delete");
            $this->output->newLine();

            $bar = $this->output->createProgressBar($idCount);
            $bar->start();

            foreach ($idsToDelete as $publicId) {
                $this->info("Deleting {$publicId}...");
                $cloudinary->uploadApi()->destroy($publicId);
                $bar->advance();
            }

            $bar->finish();

            $this->output->newLine();
            $this->output->newLine();

            $this->info('All done.');
        } else {
            $this->info('No stray photos found!');
        }
    }
}
