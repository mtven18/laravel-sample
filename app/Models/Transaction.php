<?php

namespace App\Models;

use App\Enum\Currency;
use App\Enum\TransactionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DB fields
 * @property int $id
 * @property string $from_user_id
 * @property string $to_user_id
 * @property Currency $currency
 * @property float $amount
 * @property TransactionStatus $status
 * @property Carbon|null $completed_at
 * @property string|null $failed_reason
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations
 * @property User $fromUser
 * @property User $toUser
 *
 * @method static Builder|self query()
 * @method Builder|self userTransactions(string $userId)
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'amount',
        'status',
        'failed_reason',
    ];

    protected $casts = [
        'currency' => Currency::class,
        'amount' => 'float',
        'status' => TransactionStatus::class,
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id')->withTrashed();
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id')->withTrashed();
    }

    public function scopeUserTransactions(Builder $q, string $userId): Builder
    {
        return $q->where(
            fn (Builder $q) => $q
                ->where('from_user_id', $userId)
                ->orWhere('to_user_id', $userId)
        );
    }
}
