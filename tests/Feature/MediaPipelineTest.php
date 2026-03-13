<?php

declare(strict_types=1);

use App\Events\MediaStatusUpdated;
use App\Jobs\DeleteRemoteMediaAssets;
use App\Jobs\ProcessAvatarUpload;
use App\Jobs\ProcessPostMedia;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\User;
use App\Services\ImageProcessor;
use App\Services\MediaPathGenerator;
use App\Services\VideoProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->login();

    Storage::fake('media-temp');
    Storage::fake('r2');

    config([
        'media.disk' => 'r2',
        'media.temporary_disk' => 'media-temp',
        'media.url_ttl_minutes' => 15,
        'broadcasting.default' => 'log',
    ]);
});

it('allows text-only posts without media', function (): void {
    $response = $this->postJson('/api/posts', [
        'body' => 'hello world',
    ]);

    $response->assertStatus(201);

    expect(Post::count())->toBe(1);
    expect(PostMedia::count())->toBe(0);
});

it('creates pending media records and queues image processing', function (): void {
    Queue::fake();

    $response = $this->post('/api/posts', [
        'body' => 'hello world',
        'media' => [
            UploadedFile::fake()->image('photo.jpg', 2400, 1800),
        ],
    ]);

    $response->assertStatus(201);

    $media = PostMedia::first();

    expect($media)->not()->toBeNull();
    expect($media->status)->toBe('pending');
    expect($media->temporary_path)->not()->toBeNull();

    Queue::assertPushed(ProcessPostMedia::class);
});

it('validates too many files and unsupported types', function (): void {
    $files = array_fill(0, 6, UploadedFile::fake()->image('photo.jpg'));

    $this->post('/api/posts', [
        'body' => 'hello world',
        'media' => $files,
    ], ['Accept' => 'application/json'])->assertStatus(422);

    $this->post('/api/posts', [
        'media' => [
            UploadedFile::fake()->create('notes.txt', 5, 'text/plain'),
        ],
    ], ['Accept' => 'application/json'])->assertStatus(422);
});

it('marks image media ready after processing', function (): void {
    Event::fake([MediaStatusUpdated::class]);

    $this->post('/api/posts', [
        'body' => 'hello world',
        'media' => [
            UploadedFile::fake()->image('photo.jpg', 3200, 2400),
        ],
    ])->assertStatus(201);

    $media = PostMedia::first();

    app(ProcessPostMedia::class, ['postMediaId' => $media->id])->handle(
        app(ImageProcessor::class),
        app(MediaPathGenerator::class),
        app(VideoProcessor::class),
    );

    $media->refresh();

    expect($media->status)->toBe('ready');
    expect($media->path)->not()->toBeNull();
    expect($media->url)->not()->toBeNull();

    Storage::disk('r2')->assertExists($media->path);
    Event::assertDispatched(MediaStatusUpdated::class);
});

it('marks video media ready after processing', function (): void {
    Event::fake([MediaStatusUpdated::class]);

    app()->instance(VideoProcessor::class, new class extends VideoProcessor
    {
        public function process(string $sourcePath): array
        {
            $videoPath = tempnam(sys_get_temp_dir(), 'processed-video-');
            $posterPath = tempnam(sys_get_temp_dir(), 'processed-poster-');

            file_put_contents($videoPath, 'video');
            file_put_contents($posterPath, 'poster');

            return [
                'path' => $videoPath,
                'poster_path' => $posterPath,
                'width' => 1920,
                'height' => 1080,
                'mime_type' => 'video/mp4',
                'bytes' => filesize($videoPath),
                'duration_seconds' => 3,
                'extension' => 'mp4',
            ];
        }
    });

    $this->post('/api/posts', [
        'body' => 'hello world',
        'media' => [
            UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4'),
        ],
    ])->assertStatus(201);

    $media = PostMedia::first();

    app(ProcessPostMedia::class, ['postMediaId' => $media->id])->handle(
        app(ImageProcessor::class),
        app(MediaPathGenerator::class),
        app(VideoProcessor::class),
    );

    $media->refresh();

    expect($media->status)->toBe('ready');
    expect($media->poster_path)->not()->toBeNull();

    Storage::disk('r2')->assertExists($media->path);
    Storage::disk('r2')->assertExists($media->poster_path);
});

it('marks failed processing attempts and notifies the owner', function (): void {
    Event::fake([MediaStatusUpdated::class]);

    app()->instance(VideoProcessor::class, new class extends VideoProcessor
    {
        public function process(string $sourcePath): array
        {
            throw new RuntimeException('boom');
        }
    });

    $this->post('/api/posts', [
        'body' => 'hello world',
        'media' => [
            UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4'),
        ],
    ])->assertStatus(201);

    $media = PostMedia::first();

    $job = app(ProcessPostMedia::class, ['postMediaId' => $media->id]);

    try {
        $job->handle(
            app(ImageProcessor::class),
            app(MediaPathGenerator::class),
            app(VideoProcessor::class),
        );
    } catch (Throwable $exception) {
        $job->failed($exception);
    }

    $media->refresh();

    expect($media->status)->toBe('failed');
    expect($media->failure_message)->toBe('We could not process this upload.');

    Event::assertDispatched(MediaStatusUpdated::class);
});

it('processes avatar uploads through the queue', function (): void {
    Event::fake([MediaStatusUpdated::class]);

    $this->put('/api/profile/avatar', [
        'avatar' => UploadedFile::fake()->image('avatar.jpg', 800, 800),
    ])->assertStatus(200);

    app(ProcessAvatarUpload::class, ['userId' => auth()->id()])->handle(
        app(ImageProcessor::class),
        app(MediaPathGenerator::class),
    );

    $user = auth()->user()->fresh();

    expect($user->avatar_status)->toBe('ready');
    expect($user->avatar)->not()->toBeNull();
    expect($user->avatar_url)->not()->toBeNull();

    Storage::disk('r2')->assertExists($user->avatar);
    Event::assertDispatched(MediaStatusUpdated::class);
});

it('queues remote cleanup when deleting a post with ready media', function (): void {
    Queue::fake();

    $post = create(Post::class, ['user_id' => auth()->id()]);
    $media = create(PostMedia::class, ['post_id' => $post->id]);

    $this->deleteJson('/api/posts/' . $post->id)->assertStatus(204);

    Queue::assertPushed(DeleteRemoteMediaAssets::class, function (DeleteRemoteMediaAssets $job) use ($media): bool {
        return in_array($media->path, $job->paths, true);
    });
});

it('hides failure messages from non-owners', function (): void {
    $owner = auth()->user();
    $friend = create(User::class);
    $this->createFriendship($owner, $friend);

    $post = create(Post::class, ['user_id' => $owner->id]);
    create(PostMedia::class, [
        'post_id' => $post->id,
        'status' => 'failed',
        'failure_message' => 'top secret',
    ]);

    $this->login($friend);

    $response = $this->getJson('/api/posts/' . $post->id)->assertStatus(200);

    expect(data_get($response->json(), 'media.0.failure_message'))->toBeNull();
});
