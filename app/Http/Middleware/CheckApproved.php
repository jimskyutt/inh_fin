<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Allow access to verification and logout routes
        if ($request->routeIs('verification.*', 'logout')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!$user) {
            return $next($request);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Check if user is approved by admin
        if ($user->status !== 'verified') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending admin approval. You will be notified once approved.');
        }

        return $next($request);
    }
}
