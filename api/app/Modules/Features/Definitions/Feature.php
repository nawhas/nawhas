<?php

declare(strict_types=1);

namespace App\Modules\Features\Definitions;

use App\Entities\User;

interface Feature
{
    public function name(): string;
    public function enabled(?User $user): bool;
}
