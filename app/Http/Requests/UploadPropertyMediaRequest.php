<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UploadPropertyMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
        'file' => [
            'required',
            'file',
            'mimes:jpg,jpeg,png,webp,avif,mp4,hevc',

            // Default max for images (5MB)
            Rule::when(
                fn () => $this->file && str_starts_with($this->file->getMimeType(), 'image'),
                ['max:5120']
            ),

            // Larger max for videos (e.g., 50MB)
            Rule::when(
                fn () => $this->file && str_starts_with($this->file->getMimeType(), 'video'),
                ['max:51200']
            ),
        ],
    ];

    }
}
