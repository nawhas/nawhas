<?php

declare(strict_types=1);

namespace App\Modules\Library\Services;

use App\Modules\Library\Events\ReciterCreated;
use App\Modules\Library\Models\Reciter;
use Illuminate\Events\Dispatcher;
use Ramsey\Uuid\Uuid;

class ReciterService
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function createReciter(string $name, ?string $description = null, ?string $avatar = null): Reciter
    {
        $id = (string)Uuid::uuid1();

        $this->dispatcher->dispatch(new ReciterCreated($id, [
            'name' => $name,
            'description' => $description,
            'avatar' => $avatar,
        ]));

        return Reciter::findOrFail($id);
    }
}
