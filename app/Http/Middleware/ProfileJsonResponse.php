<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!app()->bound('debugbar') || !app('debugbar')->isEnabled()) {
            return $response;
        }

        if ($response instanceof JsonResponse && $request->has('_debug')) {
            $response->setData(array_merge(
                $response->getData(true),
                ['_debugbar' => Arr::only(app('debugbar')->getdata(true),'queries')]
            ));
        }

        return $response;
    }
}
