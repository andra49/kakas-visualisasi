<?php

namespace App\Http\Middleware;

use Closure;

class CORSSetup
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
        if ($request->getMethod() == "OPTIONS") {
            $headers = array(
                'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers'=> 'X-Requested-With, content-type',);
            return Response::make('', 200, $headers);
        }
        return $next($request);
    }
}
