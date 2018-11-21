<?php

namespace Knot\Http\Middleware;

use Closure;

class CorsMiddleware extends \Spatie\Cors\Cors
{
    /**
     * The URIs that should be excluded from CORS verification.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            header('Access-Control-Allow-Origin: *');

            return $next($request);
        }

        return parent::handle($request, $next);
    }

    /**
     * Determine if the request has a URI that should pass through CORS verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
