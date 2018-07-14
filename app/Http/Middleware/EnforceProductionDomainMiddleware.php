<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class EnforceProductionDomainMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (App::environment() == "production") {
            if ($request->server->get("HTTP_HOST") != "tweekly.fm") {
                return Redirect::to("https://tweekly.fm");
            }
        }

        return $next($request);
    }
}
