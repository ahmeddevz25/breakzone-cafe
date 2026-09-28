<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Visitor;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        // Skip background AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return $next($request);
        }

        try {
            Visitor::updateOrCreate(
                [
                    'ip_address' => $request->ip(),
                    'visit_date' => today()
                ],
                [
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'page_url' => substr((string) $request->url(), 0, 250)
                ]
            );
        } catch (\Throwable $e) {
            // Silently ignore visitor logging issues to never break web requests
        }

        return $next($request);
    }
}
