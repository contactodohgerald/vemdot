<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ReturnTemplate;

class UserBlockStatus
{
    use ReturnTemplate;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->status === 'blocked') {
            return $this->returnMessageTemplate(false, "Your account has been disabled, please contact the admin");
        }

        return $next($request);
    }

}
