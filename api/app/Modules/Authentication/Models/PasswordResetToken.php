<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Models;

use App\Modules\Core\Models\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Modules\Authentication\Models\PasswordResetToken
 *
 * @property string $id
 * @property string $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Modules\Authentication\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordResetToken query()
 * @mixin \Eloquent
 */
class PasswordResetToken extends Model
{
    use HasUuid;

    protected $table = 'password_resets';

    public $timestamps = false;

    protected $dates = [
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'token',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function retrieve(string $token): self
    {
        /** @var self $model */
        $model = self::query()
            ->where('token', $token)
            ->firstOrFail();

        return $model;
    }

    public function expired(): bool
    {
        $expires = config('auth.passwords.users.expire') * 60; // in seconds

        return Carbon::make($this->created_at)->addSeconds($expires)->isPast();
    }
}
