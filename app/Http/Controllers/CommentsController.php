<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Knot\Models\Comment;
use Knot\Models\Post;
use Knot\Notifications\CommentRepliedTo;
use Knot\Notifications\PostCommentedOn;
use Knot\Traits\AddsLocation;
use Notification;

class CommentsController extends Controller
{
    use AddsLocation;

    /**
     * Display a listing of the resource.
     *
     * @param Knot\Models\Post $post
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->comments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\Post        $post
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('can_view_post', $post);

        $this->validate($request, ['body' => 'required']);

        $comment = $post->addComment([
            'user_id' => auth()->id(),
            'body' => $request->input('body'),
        ]);

        if ($request->filled('location')) {
            $location = $this->setLocation($request, $comment);
            if ($location instanceof \Illuminate\Http\Response) {
                return response($location->getOriginalContent(), 422);
            }
        }

        $comment->load('user', 'location');

        if ($post->user->id !== auth()->id()) {
            $post->user->notify(new PostCommentedOn($comment));
        }

        $replyNotificationUsers = $post->comments->map(function ($item) {
            return $item->user;
        })->reject(function ($user) use ($post) {
            return $user->id == $post->user->id || $user->id == auth()->id();
        });
        if (count($replyNotificationUsers)) {
            Notification::send($replyNotificationUsers, new CommentRepliedTo($comment));
        }

        return $comment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\Comment     $comment
     *
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
     * @param \Knot\Models\Comment $comment
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $comment->delete();

        return response([], 204);
    }
}
