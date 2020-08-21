<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Requests\Traits;

use App\Modules\Library\Models\Album;

trait HasAlbumRouteParameter
{
    public function album(): Album
    {
        return $this->route()->parameter('album');
    }
}
