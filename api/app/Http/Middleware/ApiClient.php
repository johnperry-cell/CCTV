<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiClient {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $client = $request->user();
        $origin = $request->headers->get("Origin");
        if (!$origin || $client->domain !== $origin) {
            return response(["message" => "Forbidden"], 403);
        }
        return $next($request);
    }
}
