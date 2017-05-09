<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use FamJam\Models\Post;
use FamJam\Models\Reaction;

class ReactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

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

        return $post;
    }
}
