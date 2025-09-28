<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    /**
     * Check the verification status of a user
     *
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(User $user): JsonResponse
    {
        return response()->json([
            'status' => $user->status,
            'message' => $this->getStatusMessage($user->status)
        ]);
    }

    /**
     * Get the status message based on verification status
     *
     * @param  string  $status
     * @return string
     */
    protected function getStatusMessage(string $status): string
    {
        return match ($status) {
            'verified' => 'Your account has been verified.',
            'rejected' => 'Your account verification was rejected.',
            default => 'Your account is pending verification.',
        };
    }
}
