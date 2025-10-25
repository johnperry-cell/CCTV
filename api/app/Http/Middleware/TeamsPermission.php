<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TeamsPermission {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        if (config("permission.teams") && Auth::check() && Auth::user()) {
            // session value set on login
            setPermissionsTeamId(session("team_id"));
        }
        // other custom ways to get team_id
        /*if(!empty(auth('api')->user())){
                // `getTeamIdFromToken()` example of custom method for getting the set team_id 
                setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
            }*/
        return $next($request);
    }
}
