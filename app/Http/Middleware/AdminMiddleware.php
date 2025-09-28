<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === User::ROLE_ADMIN) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Unauthorized access.');
    }
}
