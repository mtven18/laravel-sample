<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\User\TransactionUserResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_user' => new TransactionUserResource($this->fromUser),
            'to_user' => new TransactionUserResource($this->toUser),
            'amount' => $this->amount,
            'status' => $this->status,
            'created_at' => $this->created_at->timestamp,
            'completed_at' => $this->completed_at?->timestamp,
        ];
    }
}
