<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class IsEmailVerified
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
        if (auth()->user()->status === User::UNVERIFIED) {
            if ($request->expectsJson()) {
                return response()->json(['type' => 'info', 'message' => __('signin.activate')]);
            }

            return redirect()->route('auth.logout');
        }

        return $next($request);
    }
}
