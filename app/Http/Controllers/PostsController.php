<?php

namespace Knot\Http\Controllers;

use Notification;
use Knot\Models\Post;
use Illuminate\Http\Request;
use Knot\Traits\AddsLocation;
use Knot\Notifications\AddedPost;
use Knot\Traits\AddsAccompaniments;
use Knot\Services\MediaUploadService;

class PostsController extends Controller
{
    use AddsLocation, AddsAccompaniments;

    protected $uploadService;

    public function __construct(MediaUploadService $uploadService)
    {
        $this->middleware('auth:api');
        $this->uploadService = $uploadService;
    }

    public function store(Request $request)
    {
        $post = auth()->user()->posts()->create([
            'body' => $request->input('body')
        ]);

        if ($request->filled('location')) {
            $this->setLocation($request, $post);
        }
        if ($request->filled('accompaniments')) {
            $this->setAccompaniments($request, $post);
        }

        if ($request->hasFile('media')) {
            // TODO: Do some validation here you absolute buffoon.
            foreach($request->file('media') as $media) {
                $upload = $this->uploadService->uploadImage($media);
                $post->media()->create([
                    'path' => $upload['publicId'],
                    'width' => $upload['width'],
                    'height' => $upload['height'],
                    'type' => 'image',
                ]);
            }
        }

        $friendsToNotify = auth()->user()->getFriends();
        if (count($friendsToNotify->all())) {
            Notification::send($friendsToNotify, new AddedPost($post));
        }

        return $post->load('location', 'user', 'comments', 'reactions.user', 'accompaniments', 'media');
    }

    public function show(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->load(['location', 'user', 'comments', 'reactions.user', 'accompaniments']);
    }

    public function destroy(Post $post)
    {
        $this->authorize('can_modify_or_delete_post', $post);

        $post->delete();

        return response([], 204);
    }
}
