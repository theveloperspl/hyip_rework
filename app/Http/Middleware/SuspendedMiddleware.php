<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class SuspendedMiddleware
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
        if (auth()->user()->status === User::SUSPENDED) {
            if ($request->expectsJson()) {
                return response()->json(['type' => 'error', 'message' => __('signin.suspended')]);
            } else if ($request->ajax()) {
                return response()->view('errors.cards.suspended');
            }

            return redirect()->route('auth.logout');
        }

        return $next($request);
    }
}
