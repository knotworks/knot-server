<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class ImageProcessor
{
    /**
     * @return array{path: string, width: int, height: int, mime_type: string, bytes: int, extension: string}
     */
    public function process(string $sourcePath): array
    {
        [$width, $height, $imageType] = $this->imageSize($sourcePath);
        $orientation = $this->readOrientation($sourcePath);
        $sourceImage = $this->createImage($sourcePath, $imageType);

        [$sourceImage, $width, $height] = $this->orientImage($sourceImage, $orientation, $width, $height);
        [$targetWidth, $targetHeight] = $this->scaledDimensions($width, $height, (int) config('media.max_image_dimension'));

        $outputImage = $sourceImage;

        if ($targetWidth !== $width || $targetHeight !== $height) {
            $outputImage = imagecreatetruecolor($targetWidth, $targetHeight);
            if ($outputImage === false) {
                imagedestroy($sourceImage);

                throw new RuntimeException('Unable to resize image.');
            }

            imagealphablending($outputImage, false);
            imagesavealpha($outputImage, true);

            $wasResampled = imagecopyresampled(
                $outputImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $width,
                $height,
            );

            if ($wasResampled === false) {
                imagedestroy($outputImage);
                imagedestroy($sourceImage);

                throw new RuntimeException('Unable to resize image.');
            }
        }

        $mimeType = image_type_to_mime_type($imageType);
        $extension = match ($mimeType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $outputPath = tempnam(sys_get_temp_dir(), 'knot-image-');
        if ($outputPath === false) {
            throw new RuntimeException('Unable to create temporary image.');
        }

        $finalOutputPath = $outputPath . '.' . $extension;
        rename($outputPath, $finalOutputPath);

        $this->writeImage($outputImage, $finalOutputPath, $extension);

        if ($outputImage !== $sourceImage) {
            imagedestroy($outputImage);
        }

        imagedestroy($sourceImage);

        return [
            'path' => $finalOutputPath,
            'width' => $targetWidth,
            'height' => $targetHeight,
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

    /**
     * @return resource|\GdImage
     */
    private function createImage(string $sourcePath, int $imageType)
    {
        $image = match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unsupported image format.');
        }

        return $image;
    }

    /**
     * @param  resource|\GdImage  $image
     * @return array{0: resource|\GdImage, 1: int, 2: int}
     */
    private function orientImage($image, int $orientation, int $width, int $height): array
    {
        if ($orientation === 3) {
            $rotated = imagerotate($image, 180, 0);
        } elseif ($orientation === 6) {
            $rotated = imagerotate($image, -90, 0);
            [$width, $height] = [$height, $width];
        } elseif ($orientation === 8) {
            $rotated = imagerotate($image, 90, 0);
            [$width, $height] = [$height, $width];
        } else {
            return [$image, $width, $height];
        }

        if ($rotated === false) {
            throw new RuntimeException('Unable to orient image.');
        }

        imagedestroy($image);

        return [$rotated, $width, $height];
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

    /**
     * @param  resource|\GdImage  $image
     */
    private function writeImage($image, string $outputPath, string $extension): void
    {
        $quality = (int) config('media.image_quality');

        $wasWritten = match ($extension) {
            'png' => imagepng($image, $outputPath, 9),
            'webp' => imagewebp($image, $outputPath, $quality),
            default => imagejpeg($image, $outputPath, $quality),
        };

        if ($wasWritten === false) {
            throw new RuntimeException('Unable to write processed image.');
        }
    }

    private function readOrientation(string $sourcePath): int
    {
        if (! function_exists('exif_read_data')) {
            return 1;
        }

        $exif = @exif_read_data($sourcePath);

        return (int) ($exif['Orientation'] ?? 1);
    }
}
