<?php

namespace App\Http\Controllers;

use App\Facades\Localer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function update(Request $request, string $lang): RedirectResponse
    {
        //check if language is even supported
        if (!Localer::isSupported($lang)) {
            $lang = Localer::fallback();
        }
        //set cookie for persistent settings
        Cookie::queue('lang', $lang, time() + 60 * 60 * 24 * 365);
        //check if user is authenticated and update language in database
        if ($request->user()) {
            $request->user()->language = $lang;
            $request->user()->save();
        }

        return back();
    }

    public function datatablesTranslations(Request $request): JsonResponse
    {
        return response()->json([
            'emptyTable' => '<img src="' . asset('images/reviews.png') . '" class="img-fluid h-85" style="opacity: 0.25;">',
            'info' => __('table.entries'),
            'infoEmpty' => '',
            'infoFiltered' => '',
            'lengthMenu' => __('table.display'),
            'search' => __('table.search'),
            "zeroRecords" => '<img src="' . asset('images/reviews.png') . '" class="img-fluid h-85" style="opacity: 0.25;">',
            'paginate' => [
                'next' => '<i class="flaticon-right-chevron-1" aria-hidden="true"></i>',
                'previous' => '<i class="flaticon-left-chevron-1" aria-hidden="true"></i>'
            ]
        ]);
    }
}
