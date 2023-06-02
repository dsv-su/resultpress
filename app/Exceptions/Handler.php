<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($this->isHttpException($exception) && in_array($exception->getStatusCode(), [403, 401])) {
            return redirect()->route('home')->withErrors(['error' => 'You do not have permission to view this page, redirected to home page.']);
        }

        if ($exception instanceof ModelNotFoundException) {
            return redirect()->route('home')->withErrors(['error' => 'The resource you are looking for does not exist.']);
        }

        if ($this->isHttpException($exception) && in_array($exception->getStatusCode(), [500, 400])) {
            return redirect()->route('home')->withErrors(['error' => 'Something went wrong. Please try again.']);
        }

        return parent::render($request, $exception);
    }
}
