<?php

namespace Knot\Console\Commands;

use Knot\Models\PhotoPost;
use Illuminate\Console\Command;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Storage;

class MigrateS3ToCloudinaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photos:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates all photo post photos and avatars from s3 to Cloudinary';

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
     * @return mixed
     */
    public function handle()
    {
        $posts = PhotoPost::where('cloud', true)->get();

        if (count($posts)) {
            $bar = $this->output->createProgressBar(count($posts));

            $bar->start();

            foreach ($posts as $post) {
                $s3Url = Storage::cloud()->url($post->image_path);
                Cloudder::upload($s3Url, 'photo-posts/'.pathinfo($post->image_path, PATHINFO_FILENAME));
                $post->update([
                    'image_path' => Cloudder::getPublicId(),
                    'cloud' => false,
                ]);
                $bar->advance();
            }
        }
    }
}
