<?php

namespace FamJam\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FamJam\Models\PhotoPost;
use FamJam\Traits\AddsLocation;
use FamJam\Traits\AddsAccompaniments;
use Illuminate\Http\File;
use Image;

class PhotoPostsController extends PostsController
{
    use AddsLocation, AddsAccompaniments;
    
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
        $this->validate($request, ['image' => 'required|image|max:10000']);
        
        $file = $request->file('image');

        // Resize the image, while constraining aspect ratio, and ensuring it does not upsize
        $image = Image::make($file)->encode('jpg', 75);
        
        $image->resize(1200, 1600, function ($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        // Move it to the public folder
        $thumbName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->getClientOriginalExtension();
        $image->save(public_path('images/tmp/'.$thumbName));
        $tmpImageUrl = $image->dirname.'/'.$image->basename;

        // Upload it to the cloud from the public folder
        $cloudFile = new File($tmpImageUrl);
        $cloudUrl = Storage::disk('b2')->putFile('photo-posts', $cloudFile);

        $post = PhotoPost::create([
            'body' => $request->input('body'),
            'image_path' => $cloudUrl,
            'width' => $imageWidth,
            'height' => $imageHeight,
            'user_id' => auth()->id(),
        ]);
        
        // Destroy the image instance, and remove it from the public folder
        $image->destroy();
        unlink($tmpImageUrl);
        
        if ($request->has('location')) {
            $this->setLocation($request, $post->post);
        }
        if ($request->has('accompaniments')) {
            $this->setAccompaniments($request, $post->post);
        }
        

        return $post->load('post.location', 'post.accompaniments');
    }
}
