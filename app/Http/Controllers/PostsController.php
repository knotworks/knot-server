<?php

namespace Knot\Http\Controllers;

use Knot\Models\Post;
use Knot\Models\User;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function timeline()
    {
        return auth()->user()->timeline();
    }

    public function profile(User $user)
    {
        $this->authorize('can_view_profile', $user);

        return [
            'user' => $user,
            'posts' => $user->feed(),
        ];
    }

    public function show(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->load(['location', 'postable', 'user', 'comments', 'reactions.user']);
    }

    public function destroy(Post $post)
    {
        $this->authorize('can_modify_or_delete_post', $post);

        $post->delete();

        return response([], 204);
    }
}
