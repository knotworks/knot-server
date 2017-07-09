<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Knot\Models\Comment;
use Knot\Models\Post;
use Knot\Traits\AddsLocation;
use Knot\Notifications\PostCommentedOn;

class CommentsController extends Controller
{
    use AddsLocation;
    
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param Knot\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $this->authorize('can_view_comments', $post);
        
        return $post->comments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \Knot\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('can_comment', $post);

        $this->validate($request, ['body' => 'required']);

        $comment = $post->addComment([
            'user_id' => auth()->id(),
            'body' => $request->input('body'),
        ]);

        if ($request->has('location')) {
            $this->setLocation($request, $comment);
        }

        if ($post->user->id !== auth()->id()) {
            $post->user->notify(new PostCommentedOn($post, $comment));
        }

        return $comment->load('user', 'location');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Knot\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $this->validate($request, ['body' => 'required']);

        $comment->update($request->only('body'));

        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Knot\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $comment->delete();

        return response(['status' => 'Comment deleted'], 200);
    }
}
