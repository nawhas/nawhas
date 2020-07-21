<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Requests\CreateReciterRequest;
use App\Modules\Library\Http\Resources\ReciterResource;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Services\ReciterService;
use Illuminate\Http\Resources\Json\JsonResource;

class RecitersController extends Controller
{
    private ReciterService $service;

    public function __construct(ReciterService $service)
    {
        $this->service = $service;
    }

    public function store(CreateReciterRequest $request): JsonResource
    {
        $reciter = $this->service->createReciter(
            $request->get('name'),
            $request->get('description'),
            $request->get('avatar'),
        );

        return new ReciterResource($reciter);
    }

    public function show(string $id): JsonResource
    {
        return new ReciterResource(Reciter::findOrFail($id));
    }
}
