<?php

namespace Knot\Services;

use Image;
use JD\Cloudder\Facades\Cloudder;

class MediaUploadService
{
    protected $image;
    protected $name;

    protected function processImage($file): string
    {
        $path = 'uploads/media/images/'.$this->name.'.jpg';

        $this->image = Image::make($file)->encode('jpg', config('image.upload_quality'));

        $this->image->resize(config('image.max_width'), config('image.max_height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $publicPath = public_path($path);

        $this->image->save($publicPath);

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

        return [
            'publicId' => $publicId,
            'width' => $imageWidth,
            'height' => $imageHeight,
        ];
    }
}
