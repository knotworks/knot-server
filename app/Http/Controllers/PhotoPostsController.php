<?php

namespace Knot\Http\Controllers;

use Image;
use Knot\Models\PhotoPost;
use Illuminate\Http\Request;
use Knot\Traits\AddsLocation;
use Knot\Traits\AddsAccompaniments;
use Knot\Jobs\UploadPhotoPostImageToCloud;

class PhotoPostsController extends Controller
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
        $this->validate($request, ['image' => 'required|image|max:10000']);

        $file = $request->file('image');

        // Resize the image, while constraining aspect ratio, and ensuring it does not upsize
        $image = Image::make($file)->encode('jpg', 80);

        $image->resize(1200, 1600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        // Move it to the public folder
        $thumbName = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->getClientOriginalExtension();
        $path = 'images/tmp/photo-posts/'.$thumbName;
        $image->save(public_path($path));

        // Destroy the image instance
        $image->destroy();

        // Create the post, and queue the cloud upload
        $post = PhotoPost::create([
            'body' => $request->input('body'),
            'image_path' => $path,
            'width' => $imageWidth,
            'height' => $imageHeight,
            'user_id' => auth()->id(),
        ]);

        dispatch(new UploadPhotoPostImageToCloud($post));

        if ($request->has('location')) {
            $this->setLocation($request, $post->post);
        }
        if ($request->has('accompaniments')) {
            $this->setAccompaniments($request, $post->post);
        }

        return $post->load('post.location', 'post.accompaniments');
    }
}
