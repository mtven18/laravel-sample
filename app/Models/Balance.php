<?php

namespace App\Models;

use App\Enum\Currency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DB fields
 * @property int $id
 * @property string $user_id
 * @property Currency $currency
 * @property float $amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations
 * @property User $user
 */
class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'amount',
    ];

    protected $casts = [
        'currency' => Currency::class,
        'amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
