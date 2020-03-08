<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\EntityNotFoundException;
use Closure;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use LaravelDoctrine\ORM\Contracts\UrlRoutable;
use ReflectionParameter;

class SubstituteDoctrineBindings
{
    protected Registrar $router;

    protected ManagerRegistry $registry;

    public function __construct(Registrar $router, ManagerRegistry $registry)
    {
        $this->router = $router;
        $this->registry = $registry;
    }

    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        $this->router->substituteBindings($route);

        $this->substituteImplicitBindings($route);

        return $next($request);
    }

    protected function substituteImplicitBindings(Route $route)
    {
        $parameters = $route->parameters();

        foreach ($this->signatureParameters($route) as $parameter) {
            $id = $parameters[$parameter->name];
            $this->substituteBindingFor($parameter, $id, $route);
        }
    }

    protected function substituteBindingFor(ReflectionParameter $parameter, $id, Route $route): void
    {
        $class = $parameter->getClass()->getName();
        $repository = $this->registry->getRepository($class);

        try {
            if ($parameter->getClass()->implementsInterface(UrlRoutable::class)) {
                $name = call_user_func([$class, 'getRouteKeyName']);

                $entity = $repository->findOneBy([$name => $id]);
            } else {
                $entity = $repository->find($id);
            }
        } catch (ConversionException $e) {
            $entity = null;
        }


        if (is_null($entity) && !$parameter->isDefaultValueAvailable()) {
            throw new EntityNotFoundException($class, $id);
        }

        $route->setParameter($parameter->name, $entity);
    }

    /**
     * @param Route $route
     *
     * @return ReflectionParameter[]
     */
    private function signatureParameters(Route $route)
    {
        return collect($route->signatureParameters())
            ->reject(
                function (ReflectionParameter $parameter) use ($route) {
                    return !array_key_exists($parameter->name, $route->parameters());
                }
            )
            ->reject(
                function (ReflectionParameter $parameter) {
                    return !$parameter->getClass();
                }
            )->toArray();
    }
}
