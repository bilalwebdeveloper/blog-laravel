<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferencesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sources' => 'nullable|array',
            'sources.*' => 'string', 
            'categories' => 'nullable|array',
            'categories.*' => 'integer',
            'authors' => 'nullable|array',
            'authors.*' => 'string', 
        ];
    }

    public function messages()
    {
        return [
            'sources.array' => 'Preferred sources must be an array.',
            'sources.*.string' => 'Each preferred source must be a string.',
            'categories.array' => 'Preferred categories must be an array.',
            'categories.*.integer' => 'Each preferred category must be a number.',
            'authors.array' => 'Preferred authors must be an array.',
            'authors.*.string' => 'Each preferred author must be a string.',
        ];
    }
}
