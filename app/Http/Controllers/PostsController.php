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

/**
 * @group Posts
 *
 * Create, fetch, and delete posts.
 *
 * @authenticated
 */
class PostsController extends Controller
{
    use AddsAccompaniments, AddsLocation;

    /**
     * Create a new post.
     *
     * Accepts text-only posts or posts with uploaded images/videos.
     *
     * @bodyParam location object Optional location data to attach to the post.
     * @bodyParam accompaniments array Optional accompaniment entries to attach to the post.
     *
     * @response 201 {"id":1,"body":"A new family update","user_id":1,"media":[]}
     * @response 422 {"message":"The given data was invalid.","errors":{"body":["A post body is required when no media files are attached."]}}
     */
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

    /**
     * Fetch a single post.
     *
     * @urlParam post integer required The post ID. Example: 1
     *
     * @response 200 {"id":1,"body":"A new family update","user_id":1,"comments":[],"reactions":[]}
     * @response 403 {"message":"This action is unauthorized."}
     */
    public function show(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->load(Post::relationsForViewer(auth()->user()));
    }

    /**
     * Delete one of the current user's posts.
     *
     * @urlParam post integer required The post ID. Example: 1
     *
     * @response 204 {}
     * @response 403 {"message":"This action is unauthorized."}
     */
    public function destroy(Post $post)
    {
        $this->authorize('can_modify_or_delete_post', $post);

        $post->delete();

        return response([], 204);
    }
}
