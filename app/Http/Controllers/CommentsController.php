<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use FamJam\Models\Comment;
use FamJam\Models\Post;
use FamJam\Traits\AddsLocation;

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

        return $comment->load('user', 'location');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \FamJam\Models\Comment  $comment
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
     * @param  \FamJam\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {   
        $this->authorize('can_modify_or_delete', $comment);
        
        $comment->delete();
        
        return response(['status' => 'Comment deleted'], 200);
    }
}
