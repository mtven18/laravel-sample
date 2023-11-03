<?php

namespace App\Http\Requests\Api\Transaction;

use App\Enum\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'to' => [
                'required',
                'string',
            ],
            'currency' => [
                'required',
                Rule::enum(Currency::class),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ];
    }
}
