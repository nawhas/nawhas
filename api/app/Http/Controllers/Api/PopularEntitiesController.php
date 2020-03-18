<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Transformers\AlbumTransformer;
use App\Http\Transformers\ReciterTransformer;
use App\Http\Transformers\TrackTransformer;
use App\Queries\AlbumQuery;
use App\Queries\TrackQuery;
use App\Repositories\AlbumRepository;
use App\Repositories\PopularEntitiesRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use App\Support\Pagination\PaginationState;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularEntitiesController extends Controller
{
    private const CACHE_TTL = 60 * 30; // 30 minutes.
    private PopularEntitiesRepository $repository;
    private Cache $cache;

    public function __construct(PopularEntitiesRepository $repository, Cache $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function reciters(Request $request, ReciterTransformer $transformer): JsonResponse
    {
        return $this->cache->remember(
            'controllers.popular.reciters',
            self::CACHE_TTL,
            fn() => $this->respondWithCollection($this->repository->reciters(), $transformer)
        );
    }

    public function tracks(Request $request, TrackTransformer $transformer, ReciterRepository $reciterRepo): JsonResponse
    {
        $reciter = null;

        if ($request->has('reciterId')) {
            $reciter = $reciterRepo->query()->whereIdentifier($request->get('reciterId'))->get();
        }
        $key = $reciter ? "controllers.popular.tracks::reciter:{$reciter->getId()}" : 'controllers.popular.tracks';

        return $this->cache->remember(
            $key,
            self::CACHE_TTL,
            fn() => $this->respondWithCollection($this->repository->tracks($reciter), $transformer),
        );
    }
}
