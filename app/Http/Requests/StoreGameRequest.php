<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'logo' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^images\/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|gif|svg)$/i'
            ],
        ];

        // Add unique validation only when creating
        if ($this->isMethod('post')) {
            $rules['name'][] = 'unique:games,name';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Game name must not exceed 100 characters.',
            'name.unique' => 'This game already exists.',
            'logo.regex' => 'Logo must be in the images/ folder with valid extension.',
        ];
    }
}