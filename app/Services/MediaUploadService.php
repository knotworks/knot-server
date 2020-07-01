<?php

namespace Knot\Services;

use Image;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    protected function setFileName($file): string
    {
        return strtotime('now').'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    protected function getCloudinaryUploadPath($name): string
    {
        return config('app.env').'/media/'.$name;
    }

    public function uploadImage($file)
    {
        $name = $this->setFileName($file);
        $nameWithExtension = $name.'.jpg';
        $image = Image::make($file)->resize(config('image.max_width'), config('image.max_height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('jpg', config('image.upload_quality'));

        Storage::disk('media')->put($nameWithExtension, $image);

        try {
            $filePath = Storage::disk('media')->path($nameWithExtension);
            $uploadPath = $this->getCloudinaryUploadPath($name);
            $publicId = Cloudder::upload($filePath, $uploadPath)->getPublicId();

            $imageWidth = $image->width();
            $imageHeight = $image->height();
        } catch (Exception $e) {
            throw $e;
        } finally {
            $image->destroy();
            Storage::disk('media')->delete($nameWithExtension);
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
        $nameWithExtension = $name.'.mp4';

        Storage::disk('media')->put($nameWithExtension, file_get_contents($file));

        try {
            $filePath = Storage::disk('media')->path($nameWithExtension);
            $uploadPath = $this->getCloudinaryUploadPath($name);

            $upload = Cloudder::uploadVideo($filePath, $uploadPath, [
                'video_codec' => 'auto',
                'start_offset' => 0,
                'end_offset' => 30,
                'quality' => 85,
                'timeout' => 120,
            ]);

            $result = $upload->getResult();
        } catch (Exception $e) {
            throw $e;
        } finally {
            Storage::disk('media')->delete($nameWithExtension);
        }

        return [
            'publicId' => $result['public_id'],
            'width' => $result['width'],
            'height' => $result['height'],
        ];
    }
}
