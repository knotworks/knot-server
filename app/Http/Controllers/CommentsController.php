<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentRepliedTo;
use App\Notifications\PostCommentedOn;
use App\Traits\AddsLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Validator;

class CommentsController extends Controller
{
    use AddsLocation;

    /**
     * Display a listing of the resource.
     *
     *
     * @return Response
     */
    public function index(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->comments;
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return Response
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('can_view_post', $post);

        $request->validate(['body' => 'required']);

        $comment = $post->addComment([
            'user_id' => auth()->id(),
            'body' => $request->input('body'),
        ]);

        if ($request->filled('location')) {
            $location = $this->setLocation($request, $comment);
            if ($location instanceof Validator) {
                return response(['errors' => $location->errors()], 422);
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
     *
     * @return Response
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $body = $request->validate(['body' => 'required']);

        $comment->update($body);

        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $comment->delete();

        return response([], 204);
    }
}
