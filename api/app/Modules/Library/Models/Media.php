<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Media extends Model implements TimestampedEntity
{
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;

    protected $guarded = [];
}
