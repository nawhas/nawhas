<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Traits;

use App\Modules\Library\Models\Reciter;

trait HasReciterRouteParameter
{
    public function reciter(): Reciter
    {
        /** @var Reciter */
        return $this->route('reciter');
    }
}
