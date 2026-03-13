<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => [
                Rule::requiredIf(! $this->hasFile('media')),
                'nullable',
                'string',
            ],
            'media' => [
                'nullable',
                'array',
                'max:' . config('media.max_files_per_post'),
            ],
            'media.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/webm',
                'max:' . max((int) config('media.max_image_kb'), (int) config('media.max_video_kb')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => 'A post body is required when no media files are attached.',
            'media.max' => 'You may attach up to ' . config('media.max_files_per_post') . ' files to a post.',
            'media.*.mimetypes' => 'Only JPEG, PNG, WEBP, MP4, MOV, and WEBM uploads are supported.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->file('media', []) as $index => $file) {
                $mimeType = (string) $file->getMimeType();
                $maxKilobytes = str_starts_with($mimeType, 'image/')
                    ? (int) config('media.max_image_kb')
                    : (int) config('media.max_video_kb');

                if ((int) ceil($file->getSize() / 1024) > $maxKilobytes) {
                    $validator->errors()->add("media.{$index}", "The file exceeds the allowed {$maxKilobytes} KB limit.");
                }
            }
        });
    }
}
