<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Knot\Models\Post;
use Knot\Models\Reaction;
use Knot\Notifications\PostReactedTo;

class ReactionsController extends Controller
{
    /**
     * Add a new reaction to a post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Knot\Models\Post  $post
     * @return \Illuminate\Http\Response
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
