<?php

declare(strict_types=1);

namespace App\Modules\Library\Http\Middleware;

use App\Modules\Library\Repositories\LibraryAggregateRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FlushRepository
{
    private LibraryAggregateRepository $repository;

    public function __construct(LibraryAggregateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            $this->repository->flush();
        }

        return $response;
    }
}
