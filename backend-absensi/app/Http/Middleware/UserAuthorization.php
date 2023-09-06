<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;

class UserAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->getPathInfo();
        if ($path == '/login') {
            return $next($request);
        }

        $user = auth()->user();
        $team = Team::find(1);
        $permission = explode('/', $path)[1] . ':read';
        // dd($user->teamPermissions($team));
        if (!$user->hasTeamPermission($team, $permission)) {
            return abort(403);
        }
        return $next($request);
    }
}
