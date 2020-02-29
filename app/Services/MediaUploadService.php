<?php

namespace Knot\Services;

use Image;
use JD\Cloudder\Facades\Cloudder;

class MediaUploadService
{
    public function uploadImage($file)
    {
        $name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $path = 'uploads/media/images/'.$name.'.jpg';
        $publicPath = public_path($path);
        $image = Image::make($file)->save($publicPath, config('image.upload_quality'));

        $publicId = Cloudder::upload($publicPath, config('app.env').'/media/images/'.$name)
            ->getPublicId();

        $imageWidth = $image->width();
        $imageHeight = $image->height();

        // Destroy the image instance
        $image->destroy();
        unlink($publicPath);

        return [
            'publicId' => $publicId,
            'width' => $imageWidth,
            'height' => $imageHeight,
        ];
    }

    public function uploadVideo($file)
    {
        $name = strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $path = 'uploads/media/videos/';
        $publicPath = public_path($path);
        $file->move($publicPath, $name.'.mp4');

        $filePath = $publicPath.''.$name.'.mp4';
        $upload = Cloudder::uploadVideo($filePath, config('app.env').'/media/videos/'.$name, ['start_offset' => 0, 'end_offset' => 30]);
        $result = $upload->getResult();

        unlink($filePath);

        return [
            'publicId' => $result['public_id'],
            'width' => $result['width'],
            'height' => $result['height'],
        ];
    }
}
