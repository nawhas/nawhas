<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Events\ReciterCreated;
use App\Modules\Library\Http\Requests\CreateReciterRequest;
use App\Modules\Library\Http\Resources\Reciter as ReciterResource;
use App\Modules\Library\Models\Reciter;
use App\Queries\ReciterQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class RecitersController extends Controller
{
    public function __construct(ReciterTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function store(CreateReciterRequest $request)
    {
        $fields = $request->fields();
        $fields['id'] = Uuid::uuid1()->toString();

        event(new ReciterCreated($fields));

        $reciter = Reciter::find($fields['id']);

        return new ReciterResource($reciter);
    }
}
