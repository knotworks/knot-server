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
            ->orientate()
            ->resize(config('image.max_width'), config('image.max_height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($publicPath, config('image.upload_quality'));

        return $publicPath;
    }

    public function uploadImage($file)
    {
        $this->name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $publicPath = $this->processImage($file);

        $publicId = Cloudder::upload($publicPath, config('app.env').'/media/images/'.$this->name, ['angle' => 0])
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

    public function uploadVideo($file)
    {
        $this->name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $path = 'uploads/media/videos/';
        $publicPath = public_path($path);
        $file->move($publicPath, $this->name.'.mp4');

        $filePath = $publicPath.''.$this->name.'.mp4';
        $upload = Cloudder::uploadVideo($filePath, config('app.env').'/media/videos/'.$this->name, ['start_offset' => 0, 'end_offset' => 30]);
        $result = $upload->getResult();
        $publicId = $result['public_id'];
        unlink($filePath);

        return [
            'publicId' => $publicId,
            'width' => $result['width'],
            'height' => $result['height'],
        ];
    }
}
