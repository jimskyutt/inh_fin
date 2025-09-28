<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Show the form for editing the specified review.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        // Check if the authenticated user is the homeowner who created the review
        if (Auth::user()->name !== $review->homeowner_name) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the review is still pending
        if ($review->status !== 'pending') {
            return redirect()->route('jobs.index')
                ->with('error', 'This review has already been submitted.');
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        // Check if the authenticated user is the homeowner who created the review
        if (Auth::user()->name !== $review->homeowner_name) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the review is still pending
        if ($review->status !== 'pending') {
            return redirect()->route('jobs.index')
                ->with('error', 'This review has already been submitted.');
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'review' => 'required|string|min:10|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file uploads
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $uploadedImages[] = $path;
            }
        }

        // Update the review
        $updateData = [
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'status' => 'completed', // Changed from 'submitted' to 'approved' to match enum values
            'reviewed_at' => now(),
        ];

        // Only add images if any were uploaded
        if (!empty($uploadedImages)) {
            $updateData['images'] = $uploadedImages;
        }

        $review->update($updateData);

        return redirect()->route('jobs.index')
            ->with('success', 'Thank you for your review! It has been submitted.');
    }

    /**
 * Remove the specified review (soft delete).
 *
 * @param  \App\Models\Review  $review
 * @return \Illuminate\Http\Response
 */
public function destroy(Review $review)
{
    // Check if the authenticated user is the homeowner who created the review
    if (auth()->id() !== $review->homeowner_id) {
        abort(403, 'Unauthorized action.');
    }

    // Soft delete the review
    $review->update([
        'deleted_by_owner' => true
    ]);

    return redirect()->route('jobs.index')
        ->with('success', 'Review has been deleted successfully!');
}

}
