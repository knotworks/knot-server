<?php

namespace Knot\Http\Controllers;

use Knot\Models\TextPost;
use Illuminate\Http\Request;
use Knot\Traits\AddsLocation;
use Knot\Traits\AddsAccompaniments;

class TextPostsController extends Controller
{
    use AddsLocation, AddsAccompaniments;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['body' => 'required']);

        $post = TextPost::create([
            'body' => $request->input('body'),
            'user_id' => auth()->id(),
        ]);

        if ($request->has('location')) {
            $this->setLocation($request, $post->post);
        }
        if ($request->has('accompaniments')) {
            $this->setAccompaniments($request, $post->post);
        }

        return $post->load('post.location', 'post.accompaniments');
    }
}
