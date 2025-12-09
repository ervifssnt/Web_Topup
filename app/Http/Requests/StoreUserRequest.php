<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/',
                'max:15',
                'unique:users,phone'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username must not exceed 50 characters.',
            'phone.regex' => 'Phone number must be 10-15 digits.',
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 128 characters.',
        ];
    }
}