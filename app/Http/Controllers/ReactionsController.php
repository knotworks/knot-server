<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use App\Notifications\PostReactedTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * @group Reactions
 *
 * Add or update reactions on posts.
 *
 * @authenticated
 */
class ReactionsController extends Controller
{
    /**
     * Add or update a reaction on a post.
     *
     * @urlParam post integer required The post ID. Example: 1
     *
     * @bodyParam type string required The reaction type. Example: smile
     *
     * @response 200 {"id":1,"body":"A new family update","reactions":[{"user_id":2,"type":"smile"}]}
     * @response 403 {"message":"This action is unauthorized."}
     * @response 422 {"message":"The given data was invalid.","errors":{"type":["The selected type is invalid."]}}
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('can_view_post', $post);

        $reactionType = $request->validate([
            'type' => [
                'required',
                Rule::in(array_values(Reaction::REACTIONS)),
            ],
        ]);

        $reaction = $post->reactions()->where('user_id', auth()->id())->first();

        if ($reaction) {
            $reaction->update($reactionType);
        } else {
            $reaction = $post->addReaction([
                'user_id' => auth()->id(),
                'type' => $request->input('type'),
            ]);

            if (auth()->id() !== $post->user_id) {
                $post->user->notify(new PostReactedTo($reaction));
            }
        }

        return $post->load('reactions.user');
    }
}
