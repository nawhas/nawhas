<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Http\Transformers\TrackTransformer;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Support\Pagination\PaginationState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularController extends Controller
{
    public function reciters(Request $request, ReciterTransformer $transformer): JsonResponse
    {
        $pagination = PaginationState::fromRequest($request);
        $reciters = Reciter::query()
            ->popularAllTime()
            ->withCount(['albums'])
            ->paginate($pagination->getLimit());

        return $this->respondWithCollection($reciters, $transformer);
    }

    public function tracks(Request $request, TrackTransformer $transformer): JsonResponse
    {
        $pagination = PaginationState::fromRequest($request);

        $tracks = Track::query()
            ->popularAllTime()
            ->with(['reciter', 'album'])
            ->when($request->has('reciterId'), function (Builder $builder) use ($request) {
                $reciter = Reciter::retrieve($request->get('reciterId'));
                $builder->where('reciter_id', $reciter->id);
            })
            ->paginate($pagination->getLimit());

        return $this->respondWithCollection($tracks, $transformer);
    }
}
