<?php

namespace Knot\Services;

use Image;
use JD\Cloudder\Facades\Cloudder;

class MediaUploadService
{
    protected $image;
    protected $name;

    public function processImage($file): string
    {
        $path = 'uploads/media/images/'.$this->name.'.jpg';
        $publicPath = public_path($path);

        $this->image = Image::make($file)
            ->encode('jpg', config('image.upload_quality'))
            ->orientate()
            ->save($publicPath);

        return $publicPath;
    }

    public function uploadImage($file)
    {
        $this->name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $publicPath = $this->processImage($file);

        $publicId = Cloudder::upload($publicPath, config('app.env').'/media/images/'.$this->name)
            ->getPublicId();

        $imageWidth = $this->image->width();
        $imageHeight = $this->image->height();

        // Destroy the image instance
        $this->image->destroy();
        unlink($publicPath);

        return [
            'publicId' => $publicId,
            'width' => $imageWidth,
            'height' => $imageHeight,
        ];
    }
}
