<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Library\Http\Transformers\ReciterTransformer;
use App\Modules\Library\Http\Transformers\TrackTransformer;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularController extends Controller
{
    private const CACHE_TTL = 60 * 60 * 24; // 24 hours
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function reciters(Request $request, ReciterTransformer $transformer): JsonResponse
    {
        $pagination = PaginationState::fromRequest($request);
        $reciters = Reciter::query()
            ->popularAllTime()
            ->paginate($pagination->getLimit());

        return $this->cache->remember(
            $this->getRecitersCacheKey($request),
            self::CACHE_TTL,
            fn() => $this->respondWithCollection($reciters, $transformer),
        );
    }

    public function tracks(Request $request, TrackTransformer $transformer)
    {
        $pagination = PaginationState::fromRequest($request);
        $tracks = Track::query()
            ->popularAllTime()
            ->when($request->has('reciterId'), function (Builder $builder) use ($request) {
                $reciter = Reciter::retrieve($request->get('reciterId'));
                $builder->where('reciter_id', $reciter->id);
            })
            ->paginate($pagination->getLimit());


        $key = $this->getTracksCacheKey($request, $request->get('reciterId'));

        return $this->cache->remember(
            $key,
            self::CACHE_TTL,
            fn() => $this->respondWithCollection($tracks, $transformer),
        );
    }

    private function getRecitersCacheKey(Request $request): string
    {
        $hash = $this->generateRequestHash($request);
        return "controllers.popular.reciters::{$hash}";
    }

    private function getTracksCacheKey(Request $request, ?string $reciterId = null): string
    {
        $key = $reciterId ? "controllers.popular.tracks::reciter:{$reciterId}" : 'controllers.popular.tracks';

        $hash = $this->generateRequestHash($request);
        return "$key::{$hash}";
    }

    private function generateRequestHash(Request $request): string
    {
        return md5(http_build_query($request->all()));
    }
}
