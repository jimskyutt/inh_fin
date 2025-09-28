<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request, $id = null, $hash = null)
    {
        // If using the signed URL with id and hash
        if ($id && $hash) {
            $user = User::findOrFail($id);
            
            // Verify the hash
            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                Log::warning('Invalid verification hash', ['user_id' => $user->id]);
                return redirect()->route('verification.notice')->with('error', 'Invalid verification link.');
            }

            // Check if already verified
            if ($user->hasVerifiedEmail()) {
                return $this->redirectAfterVerification($user, 'Your email is already verified.');
            }

            // Mark as verified
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
                Log::info('Email verified successfully', ['user_id' => $user->id]);
                
                // Log the user in if they're not already
                if (!Auth::check()) {
                    Auth::login($user);
                }
                
                return $this->redirectAfterVerification($user, 'Your email has been verified!');
            }
            
            return redirect()->route('verification.notice')->with('error', 'Unable to verify email.');
        }
        
        // Handle the standard verification request (for authenticated users)
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request->user(), 'Your email is already verified.');
        }
        
        return $request->expectsJson()
            ? response()->json(['message' => 'Verification link is invalid.'], 403)
            : redirect()->route('verification.notice')->with('error', 'Invalid verification link.');
    }
    
    /**
     * Redirect after successful verification
     */
    protected function redirectAfterVerification($user, $message)
    {
        // Log the user out after verification
        Auth::logout();
        
        // Redirect to login page with success message
        return redirect()->route('login')
            ->with('status', $message . ' Please log in and wait for admin approval to access your account.');
    }
}
