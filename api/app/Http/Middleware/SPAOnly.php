<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class SPAOnly {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        if (!EnsureFrontendRequestsAreStateful::fromFrontend($request)) {
            return response(
                [
                    "message" => "Resource not found!",
                ],
                404
            );
        }
        return $next($request);
    }
}
