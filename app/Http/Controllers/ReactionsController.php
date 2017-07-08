<?php

namespace Knot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Knot\Models\Post;
use Knot\Models\Reaction;

class ReactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Add a new reaction to a post
     *
     * @param \Illuminate\Http\Request $request
     * @param \Knot\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('can_react', $post);
        
        $this->validate($request, [
            'type' => [
                'required',
                Rule::in(array_values(Reaction::REACTIONS))
            ]
        ]);
        
        $reaction = Reaction::where('post_id', $post->id)->where('user_id', auth()->id())->first();
        
        if ($reaction) {
            $reaction->fill([
                'type' => $request->input('type')
            ]);
            $reaction->save();
        } else {
            $post->addReaction([
                'user_id' => auth()->id(),
                'type' => $request->input('type'),
            ]);
        }

        return $post->load('reactions.user');
    }
}
