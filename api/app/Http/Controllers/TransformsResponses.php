<?php

namespace App\Http\Controllers;

use App\Modules\Core\Transformers\Transformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;

trait TransformsResponses
{
    /**
     * @var Transformer
     */
    protected Transformer $transformer;

    /**
     * @param object $item
     */
    protected function respondWithItem($item, Transformer $transformer = null): JsonResponse
    {
        // Eager load relationships before passing it through the transformer.
        if (method_exists($item, 'loadIncludes')) {
            $item->loadIncludes(request()->get('include'));
        }

        $transformer = $transformer ?: $this->transformer;
        $resource = new FractalItem($item, $transformer);
        $rootScope = $this->getFractal()->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * @param array $array - data
     * @param array $headers - http headers
     */
    protected function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return response()->json($array, 200, $headers);
    }

    /**
     * @param mixed $collection - some collection of data
     */
    protected function respondWithCollection($collection, Transformer $transformer = null): JsonResponse
    {
        $transformer = $transformer ?: $this->transformer;
        $resource = new FractalCollection($collection, $transformer);
        $rootScope = $this->getFractal()->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * @param Paginator $paginator
     * @param Transformer|null $transformer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPaginator(Paginator $paginator, Transformer $transformer = null): JsonResponse
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
