<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Traits\Permissions;
use Closure;

class IsAdmin
{
    use Permissions;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $this->setPermissionsUser($request->user())->hasPermission('admin')) {
            return (new Controller)->buildResponse([
                'message' => 'You do not have permision to view this page.',
                'status' => 'error',
                'status_code' => 403,
            ]);
        }

        return $next($request);
    }
}
