<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Str::contains($request->url(), 'admin')) $locale = "vi";
        else $locale = match ($preferredLanguage = $request->header("Language", "vi")) {
            '*' => app()->getLocale(), // Any locale, so use application default
            default => $preferredLanguage,
        };
        app()->setLocale($locale);

        return $next($request);
    }
}
