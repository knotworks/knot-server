<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Knot\Models\Post;
use Knot\Notifications\AddedPost;
use Knot\Traits\AddsAccompaniments;
use Knot\Traits\AddsLocation;
use Notification;

class PostsController extends Controller
{
    use AddsLocation, AddsAccompaniments;

    public function store(Request $request)
    {
        $request->validate([
            'body' => [
                Rule::requiredIf(! ($request->has('media') && count($request->input('media')))),
                'nullable',
                'string',
            ],
        ]);

        $post = auth()->user()->posts()->create([
            'body' => $request->input('body'),
        ]);

        if ($request->has('media')) {
            $validator = Validator::make($request->all(), [
                'media' => 'present|array|min:0|max:5',
                'media.*.path' => 'required|string',
                'media.*.width' => 'required|integer',
                'media.*.height' => 'required|integer',
                'media.*.type' => 'required|in:image,video',
            ]);

            if ($validator->fails()) {
                $post->delete();

                return response(['errors' => $validator->errors()], 422);
            }

            foreach ($request->input('media') as $media) {
                $post->media()->create([
                    'path' => $media['path'],
                    'width' => $media['width'],
                    'height' => $media['height'],
                    'type' => $media['type'],
                ]);
            }
        }

        if ($request->filled('location')) {
            $location = $this->setLocation($request, $post);
            if ($location instanceof \Illuminate\Validation\Validator) {
                return response(['errors' => $location->errors()], 422);
            }
        }
        if ($request->filled('accompaniments')) {
            $accompaniments = $this->setAccompaniments($request, $post);
            if ($accompaniments instanceof \Illuminate\Validation\Validator) {
                return response(['errors' => $accompaniments->errors()], 422);
            }
        }

        $friendsToNotify = auth()->user()->getFriends();

        if ($friendsToNotify->isNotEmpty()) {
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
