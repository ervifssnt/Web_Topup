<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_id' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/'
            ],
            'topup_option_id' => [
                'required',
                'integer',
                'exists:topup_options,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.regex' => 'Account ID contains invalid characters.',
            'account_id.max' => 'Account ID is too long.',
            'topup_option_id.exists' => 'Invalid topup option selected.',
        ];
    }
}