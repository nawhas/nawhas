<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Media extends Model implements TimestampedEntity
{
    protected $keyType = 'string';
    protected $guarded = [];

    public function getCreatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->created_at);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->updated_at);
    }
}
