<?php

namespace App\Exceptions;

use App\Services\HttpStatus;
use App\Traits\Renderer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use Throwable;

class Handler extends ExceptionHandler
{
    use Renderer;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Request $request, Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->isXmlHttpRequest()) {
            $line = (
                $e instanceof \ErrorException ||
                $e instanceof \Error ||
                $e instanceof TransportException ||
                $e instanceof QueryException
                    ? ' in '.$e->getFile().' on line '.$e->getLine()
                    : ''
                );

            return match (true) {
                $e instanceof NotFoundHttpException ||
                $e instanceof ModelNotFoundException => $this->renderException(
                    HttpStatus::message(HttpStatus::NOT_FOUND),
                    HttpStatus::NOT_FOUND
                ),
                $e instanceof AccessDeniedHttpException => $this->renderException(
                    HttpStatus::message(HttpStatus::FORBIDDEN),
                    HttpStatus::FORBIDDEN
                ),
                $e instanceof AuthenticationException ||
                $e instanceof UnauthorizedHttpException => $this->renderException(
                    HttpStatus::message(HttpStatus::UNAUTHORIZED),
                    HttpStatus::UNAUTHORIZED
                ),
                $e instanceof MethodNotAllowedHttpException => $this->renderException(
                    HttpStatus::message(HttpStatus::METHOD_NOT_ALLOWED),
                    HttpStatus::METHOD_NOT_ALLOWED
                ),
                $e instanceof ValidationException => $this->renderException(
                    $e->getMessage(),
                    HttpStatus::UNPROCESSABLE_ENTITY,
                    ['errors' => $e->errors()]
                ),
                $e instanceof UnprocessableEntityHttpException => $this->renderException(
                    HttpStatus::message(HttpStatus::UNPROCESSABLE_ENTITY),
                    HttpStatus::UNPROCESSABLE_ENTITY
                ),
                $e instanceof ThrottleRequestsException => $this->renderException(
                    HttpStatus::message(HttpStatus::TOO_MANY_REQUESTS),
                    HttpStatus::TOO_MANY_REQUESTS
                ),
                // $e instanceof \ErrorException ||
                // $e instanceof TransportException ||
                // $e instanceof \Error ||
                // $e instanceof QueryException => $this->renderException($e->getMessage().$line, HttpStatus::SERVER_ERROR),
                default => $this->renderException(($e->getMessage() ?? 'An error occured').$line, HttpStatus::SERVER_ERROR),
            };
        }

        return parent::render($request, $e);
    }

    protected function renderException(string $msg, $code = 404, array $misc = [])
    {
        if (request()->is('api/*')) {
            return $this->buildResponse(collect([
                'message' => $msg,
                'status' => 'error',
                'status_code' => $code,
            ])->merge($misc));
        }
    }
}
