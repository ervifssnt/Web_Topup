<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:10000',
                'max:100000000'
            ],
            'payment_method' => [
                'required',
                'string',
                'in:BCA,Mandiri,BNI,GoPay,OVO,DANA'
            ],
            'proof_image' => [
                'nullable',
                'url',
                'max:500',
                'regex:/\.(jpg|jpeg|png|gif)$/i'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Minimum top-up amount is Rp 10,000.',
            'amount.max' => 'Maximum top-up amount is Rp 100,000,000.',
            'payment_method.in' => 'Invalid payment method selected.',
            'proof_image.url' => 'Proof image must be a valid URL.',
            'proof_image.regex' => 'Proof image must be a JPG, JPEG, PNG, or GIF file.',
            'notes.max' => 'Notes must not exceed 500 characters.',
        ];
    }
}