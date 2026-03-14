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

/**
 * @group Comments
 *
 * Create and manage post comments.
 *
 * @authenticated
 */
class CommentsController extends Controller
{
    use AddsLocation;

    /**
     * List a post's comments.
     *
     * @urlParam post integer required The post ID. Example: 1
     *
     * @response 200 [{"id":1,"body":"Looking good!","user_id":2,"post_id":1}]
     * @response 403 {"message":"This action is unauthorized."}
     */
    public function index(Post $post)
    {
        $this->authorize('can_view_post', $post);

        return $post->comments;
    }

    /**
     * Add a comment to a post.
     *
     * @urlParam post integer required The post ID. Example: 1
     *
     * @bodyParam body string required The comment text. Example: Looking good!
     * @bodyParam location object A location payload to attach to the comment.
     *
     * @response 201 {"id":1,"body":"Looking good!","user":{"id":2,"first_name":"Jane"}}
     * @response 403 {"message":"This action is unauthorized."}
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
     * Update one of the current user's comments.
     *
     * @urlParam comment integer required The comment ID. Example: 1
     *
     * @bodyParam body string required The updated comment text. Example: Updated comment
     *
     * @response 200 {"id":1,"body":"Updated comment","user_id":2,"post_id":1}
     * @response 403 {"message":"This action is unauthorized."}
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $body = $request->validate(['body' => 'required']);

        $comment->update($body);

        return $comment;
    }

    /**
     * Delete one of the current user's comments.
     *
     * @urlParam comment integer required The comment ID. Example: 1
     *
     * @response 204 {}
     * @response 403 {"message":"This action is unauthorized."}
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('can_modify_or_delete', $comment);

        $comment->delete();

        return response([], 204);
    }
}
