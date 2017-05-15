<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use FamJam\Models\TextPost;

class TextPostsController extends PostsController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['body' => 'required']);
        
        $post = TextPost::create([
            'body' => $request->input('body'),
            'user_id' => auth()->id(),
        ]);
        
        $this->attachPostExtras($request, $post->post);

        return $post->load('post.location', 'post.accompaniments');
    }
}
