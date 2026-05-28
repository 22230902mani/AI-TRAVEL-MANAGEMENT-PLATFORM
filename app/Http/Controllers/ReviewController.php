<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct() {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id'     => 'nullable|exists:destinations,id',
            'package_id'         => 'nullable|exists:packages,id',
            'hotel_id'           => 'nullable|exists:hotels,id',
            'booking_id'         => 'nullable|exists:bookings,id',
            'reviewable_type'    => 'required|string',
            'rating'             => 'required|integer|min:1|max:5',
            'title'              => 'nullable|string|max:200',
            'body'               => 'required|string|min:20',
            'food_rating'        => 'nullable|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'safety_rating'      => 'nullable|integer|min:1|max:5',
            'value_rating'       => 'nullable|integer|min:1|max:5',
        ]);

        $review = Review::create([
            ...$validated,
            'user_id'     => auth()->id(),
            'is_verified' => (bool) $request->booking_id,
        ]);

        // Update destination avg_rating
        if ($review->destination_id) {
            $dest = $review->destination;
            $avg  = Review::where('destination_id', $dest->id)->avg('rating');
            $count= Review::where('destination_id', $dest->id)->count();
            $dest->update(['avg_rating' => round($avg, 1), 'review_count' => $count]);
        }

        return back()->with('success', '✅ Review submitted! Thank you for your feedback.');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }

    public function markHelpful(Review $review)
    {
        $review->increment('helpful_votes');
        return response()->json(['votes' => $review->fresh()->helpful_votes]);
    }
}
