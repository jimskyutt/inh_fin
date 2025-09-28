<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request)
    {
        if ($request->user()) {
            if ($request->user()->hasVerifiedEmail()) {
                return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended(RouteServiceProvider::HOME);
            }

            $request->user()->sendEmailVerificationNotification();
        } else {
            // Handle unauthenticated verification request
            $request->validate(['email' => 'required|email']);
            
            Log::info('Verification request for email: ' . $request->email);
            
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                Log::info('User found, email verified: ' . ($user->hasVerifiedEmail() ? 'Yes' : 'No'));
                
                if (!$user->hasVerifiedEmail()) {
                    try {
                        $user->sendEmailVerificationNotification();
                        Log::info('Verification email sent to: ' . $user->email);
                    } catch (\Exception $e) {
                        Log::error('Failed to send verification email: ' . $e->getMessage());
                        return $request->wantsJson()
                            ? new JsonResponse(['error' => 'Failed to send verification email'], 500)
                            : back()->with('error', 'Failed to send verification email');
                    }
                }
            } else {
                Log::warning('No user found with email: ' . $request->email);
            }
        }

        return $request->wantsJson()
            ? new JsonResponse(['message' => 'Verification link sent!'], 202)
            : back()->with('status', 'verification-link-sent');
    }
}
