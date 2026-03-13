<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:' . config('media.max_image_kb'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar.required' => 'An avatar image is required.',
            'avatar.mimetypes' => 'Only JPEG, PNG, and WEBP avatar uploads are supported.',
        ];
    }
}
