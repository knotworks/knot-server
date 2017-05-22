<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use FamJam\Models\Post;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function feed()
    {
        return auth()->user()->feed();
    }

    protected function attachPostExtras(Request $request, Post $post)
    {
        if ($request->has('location')) {
            
            $this->validate($request, [
                'location.lat' => [
                    'required',
                    'regex:/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/'
                ],
                'location.long' => [
                    'required',
                    'regex:/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/'
                ],
                'location.name' => 'required',
            ]);
            
            $post->addLocation([
                'user_id' => auth()->id(),
                'lat' => $request->input('location.lat'),
                'long' => $request->input('location.long'),
                'name' => $request->input('location.name'),
            ]);
        }

        if ($request->has('accompaniments')) {
            $this->validate($request, [
                'accompaniments.*.name' => 'required'
            ]);

            $post->addAccompaniments($request->accompaniments);
        }
    }
}
