<?php

namespace App\Http\Middleware;

use Closure;

class CheckEmailIsAssociatedWithProject
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_null($request->project->emailAddresses()->find($request->email_address->id))) {
            return abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
