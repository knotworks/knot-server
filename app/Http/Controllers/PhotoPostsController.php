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
        $this->validate($request, ['image' => 'required|image|max:'.config('image.max_size')]);

        $file = $request->file('image');
        // Move it to the public folder
        $thumbName = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.jpg';
        $path = 'images/tmp/photo-posts/'.$thumbName;

        // Resize the image, while constraining aspect ratio, and ensuring it does not upsize
        $image = Image::make($file)->encode('jpg', config('image.upload_quality'));

        $image->resize(config('image.max_width'), config('image.max_height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $imageWidth = $image->width();
        $imageHeight = $image->height();

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
