<?php

namespace App\Exceptions;

use App\Modules\Features\Exceptions\FeatureNotEnabledException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    protected function prepareException(Throwable $e)
    {
        $e = parent::prepareException($e);

        if (app()->environment('production', 'staging')) {
            if ($e instanceof FeatureNotEnabledException) {
                return new NotFoundHttpException($e->getMessage(), $e);
            }
        }

        return $e;
    }

    public function report(Throwable $e)
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
