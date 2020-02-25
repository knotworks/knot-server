<?php

namespace Knot\Http\Controllers;

use Image;
use Notification;
use Knot\Models\PostMedia;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Knot\Notifications\AddedPost;

class PostMediaController extends Controller
{
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
        $name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $path = 'images/tmp/photo-posts/'.$name.'.jpg';

        // Resize the image, while constraining aspect ratio, and ensuring it does not upsize
        $image = Image::make($file)->encode('jpg', config('image.upload_quality'));

        $image->resize(config('image.max_width'), config('image.max_height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        $image->save(public_path($path));

        Cloudder::upload(public_path($path), 'photo-posts/'.$name);

        // Destroy the image instance
        $image->destroy();

        // Create the post, and queue the cloud upload
        $post = PhotoPost::create([
            'body' => $request->input('body'),
            'image_path' => Cloudder::getPublicId(),
            'width' => $imageWidth,
            'height' => $imageHeight,
            'user_id' => auth()->id(),
        ]);

        unlink(public_path($path));
    }
}
