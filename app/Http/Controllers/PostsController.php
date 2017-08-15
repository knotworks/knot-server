<?php

namespace Knot\Http\Controllers;

use Knot\Models\Post;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function feed()
    {
        return auth()->user()->feed();
    }

    public function show(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->load(['location', 'postable', 'user', 'comments', 'reactions.user']);
    }
}
