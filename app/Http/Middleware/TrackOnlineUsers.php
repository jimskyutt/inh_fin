<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OnlineUser;

class TrackOnlineUsers
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $sessionId = $request->session()->getId();
            $userId = Auth::id();
            
            // Remove old sessions for this user
            OnlineUser::where('user_id', $userId)
                     ->where('session_id', '!=', $sessionId)
                     ->delete();
            
            // Add current session if not exists
            OnlineUser::updateOrCreate(
                ['user_id' => $userId],
                ['session_id' => $sessionId]
            );
        }
        
        return $next($request);
    }
}