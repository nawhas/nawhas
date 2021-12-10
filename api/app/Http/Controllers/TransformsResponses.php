<?php

namespace App\Http\Controllers;

use App\Modules\Core\Transformers\Transformer;
use ArrayIterator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;

trait TransformsResponses
{
    protected Transformer $transformer;

    protected function respondWithItem(object $item, ?Transformer $transformer = null): JsonResponse
    {
        $transformer = $transformer ?: $this->transformer;
        $resource = new FractalItem($item, $transformer);
        $rootScope = $this->getFractal()->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    protected function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return response()->json($array, 200, $headers);
    }

    protected function respondWithCollection(mixed $collection, ?Transformer $transformer = null): JsonResponse
    {
        $transformer = $transformer ?: $this->transformer;
        $resource = new FractalCollection($collection, $transformer);
        $rootScope = $this->getFractal()->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    protected function respondWithPaginator(Paginator $paginator, ?Transformer $transformer = null): JsonResponse
    {
        $collection = $paginator->items();

        $transformer = $transformer ?: $this->transformer;
        $resource = new FractalCollection($collection, $transformer);

        // Append all other query params to the paginator.
        $paginator->appends(request()->except('page'));
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $rootScope = $this->getFractal()->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    private function getFractal(): FractalManager
    {
        return app(FractalManager::class);
    }
}
