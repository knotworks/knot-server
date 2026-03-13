<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Jobs\ProcessPostMedia;
use App\Models\Post;
use App\Notifications\AddedPost;
use App\Services\MediaPathGenerator;
use App\Traits\AddsAccompaniments;
use App\Traits\AddsLocation;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Validator;

class PostsController extends Controller
{
    use AddsAccompaniments, AddsLocation;

    public function store(StorePostRequest $request, MediaPathGenerator $mediaPathGenerator)
    {
        $post = auth()->user()->posts()->create([
            'body' => $request->input('body'),
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $mediaFile) {
                $media = $post->media()->create([
                    'type' => str_starts_with((string) $mediaFile->getMimeType(), 'video/') ? 'video' : 'image',
                    'disk' => config('media.disk'),
                    'status' => 'pending',
                    'sort_order' => $index,
                    'mime_type' => $mediaFile->getMimeType(),
                    'bytes' => $mediaFile->getSize(),
                ]);

                $temporaryPath = $mediaPathGenerator->temporaryPostMediaPath($media, $mediaFile);

                $mediaFile->storeAs(
                    dirname($temporaryPath),
                    basename($temporaryPath),
                    config('media.temporary_disk'),
                );

                $media->forceFill([
                    'temporary_path' => $temporaryPath,
                ])->save();

                ProcessPostMedia::dispatch($media->id)->afterCommit();
            }
        }

        if ($request->filled('location')) {
            $location = $this->setLocation($request, $post);
            if ($location instanceof Validator) {
                return response(['errors' => $location->errors()], 422);
            }
        }
        if ($request->filled('accompaniments')) {
            $accompaniments = $this->setAccompaniments($request, $post);
            if ($accompaniments instanceof Validator) {
                return response(['errors' => $accompaniments->errors()], 422);
            }
        }

        $friendsToNotify = auth()->user()->getFriends();

        if ($friendsToNotify->isNotEmpty()) {
            Notification::send($friendsToNotify, new AddedPost($post));
        }

        return $post->load(Post::relationsForViewer(auth()->user()));
    }

    public function show(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->load(Post::relationsForViewer(auth()->user()));
    }

    public function destroy(Post $post)
    {
        $this->authorize('can_modify_or_delete_post', $post);

        $post->delete();

        return response([], 204);
    }
}
