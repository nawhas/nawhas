<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Aliases;

use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use Illuminate\Database\Eloquent\Model;

abstract class Alias extends Model
{
    use HasUuid;
    use HasTimestamps;
}
