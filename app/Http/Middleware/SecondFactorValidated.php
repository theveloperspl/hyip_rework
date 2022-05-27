<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecondFactorValidated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->second_factor && auth()->user()->security->signin && !session()->get('second_factor_validated')) {
            if ($request->expectsJson()) {
                return response()->json(['type' => 'error', 'message' => __('security.invalid_code')]);
            }

            return redirect()->route('auth.logout');
        }

        return $next($request);
    }
}
