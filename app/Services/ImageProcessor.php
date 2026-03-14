<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Spatie\Image\Enums\Constraint;
use Spatie\Image\Image;

class ImageProcessor
{
    /**
     * @return array{path: string, width: int, height: int, mime_type: string, bytes: int, extension: string}
     */
    public function process(string $sourcePath): array
    {
        [, , $imageType] = $this->imageSize($sourcePath);
        $extension = $this->outputExtension($imageType);
        $image = Image::load($sourcePath)->orientation();
        [$targetWidth, $targetHeight] = $this->scaledDimensions(
            $image->getWidth(),
            $image->getHeight(),
            (int) config('media.max_image_dimension'),
        );

        $image->resize($targetWidth, $targetHeight, [
            Constraint::PreserveAspectRatio,
            Constraint::DoNotUpsize,
        ]);

        $this->applyQuality($image, $extension);

        $finalOutputPath = $this->temporaryFilePath('knot-image-', $extension);
        $image->format($extension)->save($finalOutputPath);

        return [
            'path' => $finalOutputPath,
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'mime_type' => match ($extension) {
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            },
            'bytes' => filesize($finalOutputPath) ?: 0,
            'extension' => $extension,
        ];
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function imageSize(string $sourcePath): array
    {
        $imageSize = getimagesize($sourcePath);

        if ($imageSize === false) {
            throw new RuntimeException('Unable to read image dimensions.');
        }

        return $imageSize;
    }

    private function outputExtension(int $imageType): string
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            default => throw new RuntimeException('Unsupported image format.'),
        };
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function scaledDimensions(int $width, int $height, int $maxDimension): array
    {
        $longestEdge = max($width, $height);
        if ($longestEdge <= $maxDimension) {
            return [$width, $height];
        }

        $scale = $maxDimension / $longestEdge;

        return [
            max(1, (int) round($width * $scale)),
            max(1, (int) round($height * $scale)),
        ];
    }

    private function applyQuality(Image $image, string $extension): void
    {
        $image->quality($extension === 'png' ? 10 : (int) config('media.image_quality'));
    }

    private function temporaryFilePath(string $prefix, string $extension): string
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), $prefix);

        if ($temporaryPath === false) {
            throw new RuntimeException('Unable to create temporary image.');
        }

        $finalOutputPath = $temporaryPath . '.' . $extension;

        if (! rename($temporaryPath, $finalOutputPath)) {
            @unlink($temporaryPath);

            throw new RuntimeException('Unable to create temporary image.');
        }

        return $finalOutputPath;
    }
}
