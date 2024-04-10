<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ManagerChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response

    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->rol === 'manager') {
            return $next($request);
        }
        return response()->json([
                'success' => false,
                'message' => 'Unauthorised',
                'error'=>'Unauthorised',
        ]);//->header('code', '403');
    }
}
