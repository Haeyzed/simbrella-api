<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->shouldSkipTracking($request)) {
            return $next($request);
        }

        $response = $next($request);

        dispatch(fn () => $this->trackVisit($request))->afterResponse();

        return $response;
    }

    /**
     * Determine if tracking should be skipped.
     *
     * @param Request $request
     * @return bool
     */
    private function shouldSkipTracking(Request $request): bool
    {
        return $request->is('api/admin/*') ||
            $request->is('api/auth/*') ||
            app()->environment('testing');
    }

    /**
     * Track the visitor's information.
     *
     * @param Request $request
     * @return void
     */
    private function trackVisit(Request $request): void
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        Visitor::query()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $this->getBrowser($agent),
            'device' => $agent->device() ?: 'Unknown',
            'page_visited' => $request->path(),
            'referrer' => $request->header('referer'),
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Get browser name.
     *
     * @param Agent $agent
     * @return string
     */
    private function getBrowser(Agent $agent): string
    {
        return match (true) {
            $agent->isChrome() => 'Chrome',
            $agent->isFirefox() => 'Firefox',
            $agent->isSafari() => 'Safari',
            $agent->isOpera() => 'Opera',
            $agent->isEdge() => 'Edge',
            $agent->isIE() => 'Internet Explorer',
            default => 'Other',
        };
    }
}
