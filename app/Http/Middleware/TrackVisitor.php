<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Visitor;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        Visitor::updateOrCreate(
            [
                'ip_address' => $request->ip(),
                'visit_date' => today()
            ],
            [
                'user_agent' => $request->userAgent(),
                'page_url' => $request->fullUrl()
            ]
        );

        return $next($request);
    }
}
