<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            // Each image is just a URL (or placeholder URL) — no file upload.
            'image_urls' => ['sometimes', 'array'],
            'image_urls.*' => ['url'],
        ];
    }
}
