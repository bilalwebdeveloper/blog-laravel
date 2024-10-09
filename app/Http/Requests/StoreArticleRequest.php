<?php
// app/Http/Requests/StoreArticleRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // You may implement authorization logic if needed
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'published_at' => 'nullable|date',
            'UrlToImage' => 'nullable|url',
            'url' => 'required|url',
        ];
    }
}
