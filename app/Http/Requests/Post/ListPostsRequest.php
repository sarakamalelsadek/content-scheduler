<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\BaseFormRequest;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class ListPostsRequest extends BaseFormRequest
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
            'status' => 'nullable|in:'. implode(',', [Post::STATUS_DRAFT , Post::STATUS_PUBLISHED , Post::STATUS_SCHEDULED]),
            'date_from' => 'nullable|date|required_with:date_to',
            'date_to' => 'nullable|date|required_with:date_from',

            'per_page' => 'nullable|integer|min:1',

        ];
    }
}
