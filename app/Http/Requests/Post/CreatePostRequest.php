<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseFormRequest;

class CreatePostRequest extends BaseFormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048', // max 2MB
            'scheduled_time' => 'nullable|date_format:Y-m-d H:i:s|date|after_or_equal:now',
            'platform_ids' => 'required|array',
            'platform_ids.*' => 'required|distinct|exists:platforms,id',
        ];
    }
}
