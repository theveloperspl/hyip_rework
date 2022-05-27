<?php

namespace App\Http\Middleware;

use App\Facades\Localer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Set app language
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $desiredLocale = Localer::fallback();
        //check for language cookie
        if (Cookie::has('lang') && Localer::isSupported(Cookie::get('lang'))) {
            $desiredLocale = Cookie::get('lang');
        }
        //check if user is authenticated
        $user = $request->user();
        if ($user && Localer::isSupported($user->language)) {
            $desiredLocale = $user->language;
        }
        //set right locale
        Localer::set($desiredLocale);

        return $next($request);
    }
}
