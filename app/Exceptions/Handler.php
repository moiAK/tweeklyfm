<?php namespace App\Exceptions;

use Exception;
use Guzzle\Common\Exception\InvalidArgumentException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
        'Illuminate\Database\Eloquent\ModelNotFoundException',
        'Illuminate\Session\TokenMismatchException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return \Response::view("errors.invalid-user", array(), 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return \Response::view("errors.404", array(), 404);
        }

        // API Errors
        if ($e instanceof \GuzzleHttp\Exception\ClientException) {
            return \Response::view("errors.api", array(), 500);
        }

        if ($e instanceof \League\OAuth1\Client\Credentials\CredentialsException) {
            return \Response::view("errors.api", array(), 500);
        }

        if ($e instanceof InvalidArgumentException) {
            return \Response::view("errors.api", array(), 500);
        }

        // Otherwise error on a 500
        // return \Response::view("errors.500", array(), 500);
        return parent::render($request, $e);
    }
}
