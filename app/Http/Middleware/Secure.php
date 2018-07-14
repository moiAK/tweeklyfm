<?php

/*
 * This file is part of tweeklyfm/tweeklyfm
 *
 *  (c) Scott Wilcox <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\Middleware;

/**
 * Secure
 * Redirects any non-secure requests to their secure counterparts.
 *
 * @param request The request object.
 * @param $next The next closure.
 *
 * @return redirects to the secure counterpart of the requested uri.
 */
class Secure implements Middleware
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle($request, Closure $next)
    {
        $request->setTrustedProxies([$request->getClientIp()]);

        if (!$request->secure() && $this->app->environment() === 'production') {
            return redirect("https://{$_SERVER['HTTP_HOST']}".$request->getRequestUri());
        }

        return $next($request);
    }
}
