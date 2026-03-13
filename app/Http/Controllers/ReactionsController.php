<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use App\Notifications\PostReactedTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ReactionsController extends Controller
{
    /**
     * Add a new reaction to a post.
     *
     *
     * @return Response
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
