<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already rated this provider
        $existingRating = Rating::where('user_id', Auth::id())
            ->where('service_provider_id', $validated['service_provider_id'])
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? $existingRating->comment,
            ]);
        } else {
            // Create new rating
            Rating::create([
                'user_id' => Auth::id(),
                'service_provider_id' => $validated['service_provider_id'],
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for your review!');
    }
}
