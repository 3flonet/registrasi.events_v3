<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika ada parameter ?lang= di URL, simpan ke session
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['en', 'id'])) {
                Session::put('locale', $lang);
            }
        }

        // Set locale aplikasi berdasarkan session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        return $next($request);
    }
}
