<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e) {
            throw new JsonApi\NotFoundHttpException;
        });

        $this->renderable(function (BadRequestHttpException $e) {
            throw new JsonApi\BadRequestHttpException($e->getMessage());
        });
    }

    public function invalidJson($request, ValidationException $exception): JsonApiValidationErrorResponse
    {
        return new JsonApiValidationErrorResponse($exception);
    }
}
