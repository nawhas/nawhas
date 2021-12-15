<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Traits;

use App\Modules\Library\Models\Track;

trait HasTrackRouteParameter
{
    public function track(): Track
    {
        /** @var Track */
        return $this->route('track');
    }
}
