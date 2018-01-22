<?php

namespace Knot\Jobs;

use Illuminate\Http\File;
use Knot\Models\PhotoPost;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class UploadPhotoPostImageToCloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(PhotoPost $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $tmpPath = $this->post->image_path;
        $file = new File($tmpPath);
        try {
            $url = Storage::cloud()->putFile('photo-posts', $file, 'public');
            $this->post->fill(['image_path' => $url, 'cloud' => true])->save();
            unlink($tmpPath);
        } catch (Exception $e) {
            logger()->error('Cloud Upload Failed.', [
                'post' => $this->post->toArray(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
