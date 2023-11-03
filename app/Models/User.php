<?php

namespace App\Models;

use App\Models\Concerns\UsesUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * DB fields
 * @property string $id
 * @property string $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * Relations
 * @property Collection<Balance> $balances
 * @property Collection<Transaction> $sentTransactions
 * @property Collection<Transaction> $receivedTransactions
 *
 * Accessors
 * @property string $securityEmail
 *
 * @method static Builder|self query()
 * @method Builder|self whereUsername(string $username)
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use UsesUUID;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class);
    }

    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_user_id');
    }

    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_user_id');
    }

    public function securityEmail(): Attribute
    {
        return Attribute::get(
            function (mixed $value, array $attributes) {
                preg_replace_callback('~(^.)(.*?)(.@)~', function (array $matches) {
                    return $matches[1] // first char
                        .str_pad('', strlen($matches[2]), '*') // change characters to '*'
                        .$matches[3]; // last char before '@'
                }, $attributes['email']);
            }
        );
    }

    public function scopeWhereUsername(Builder $query, string $username): Builder
    {
        return $query->where('username', $username)
            ->orWhere('email', $username);
    }
}
