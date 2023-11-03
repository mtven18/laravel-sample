<?php

namespace App\Http\Requests\Api\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:6',
                'max:32',
                Rule::unique(User::class)->withoutTrashed(),
                'regex:~[A-Za-z0-9]~',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique(User::class)->withoutTrashed(),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:32',
                Password::default(),
            ],
        ];
    }
}
