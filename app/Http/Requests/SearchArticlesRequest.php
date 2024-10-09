<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchArticlesRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this if you have authorization logic
    }

    public function rules()
    {
        return [
            'query' => 'required|string|min:1',
            'date' => 'nullable|date',
            'source' => 'nullable|string|max:255',
            'category' => 'nullable|integer|max:255',
        ];
    }
}
