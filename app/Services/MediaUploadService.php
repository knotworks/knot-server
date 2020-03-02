<?php

namespace Knot\Services;

use Image;
use JD\Cloudder\Facades\Cloudder;

class MediaUploadService
{
    protected function setFileName($file): string {
        return strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    public function uploadImage($file)
    {
        $name = $this->setFileName($file);
        $publicPath = public_path('uploads/media/images/'.$name.'.jpg');

        $image = Image::make($file)->resize(config('image.max_width'), config('image.max_height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($publicPath, config('image.upload_quality'));

        try {
            $uploadPath = config('app.env').'/media/images/'.$name;
            $publicId = Cloudder::upload($publicPath, $uploadPath)->getPublicId();

            $imageWidth = $image->width();
            $imageHeight = $image->height();
        } catch (Exception $e) {
            throw $e;
        } finally {
            $image->destroy();
            unlink($publicPath);
        }

        return [
            'publicId' => $publicId,
            'width' => $imageWidth,
            'height' => $imageHeight,
        ];
    }

    public function uploadVideo($file)
    {
        $name = $this->setFileName($file);
        $publicPath = public_path('uploads/media/videos/');
        $file->move($publicPath, $name.'.mp4');

        $filePath = $publicPath.$name.'.mp4';
        try {
            $upload = Cloudder::uploadVideo($filePath, config('app.env').'/media/videos/'.$name, ['start_offset' => 0, 'end_offset' => 30, 'quality' => 85, 'timeout' => 90]);
            $result = $upload->getResult();
        } catch (Exception $e) {
            throw $e;
        } finally {
            unlink($filePath);
        }


        return [
            'publicId' => $result['public_id'],
            'width' => $result['width'],
            'height' => $result['height'],
        ];
    }
}
