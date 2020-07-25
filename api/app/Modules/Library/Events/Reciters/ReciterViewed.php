<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReciterViewed extends ShouldBeStored
{
    public string $id;
    public array $data;

    public function __construct(string $id, array $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }
}
