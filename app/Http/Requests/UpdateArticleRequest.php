<?php
// app/Http/Requests/UpdateArticleRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // You may implement authorization logic if needed
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'author' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'published_at' => 'sometimes|nullable|date',
            'UrlToImage' => 'sometimes|nullable|url',
            'url' => 'sometimes|required|url',
        ];
    }
}
