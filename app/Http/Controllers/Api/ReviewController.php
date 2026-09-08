<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // POST /api/reviews
    public function store(StoreReviewRequest $request)
    {
        $review = Review::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Review created successfully.',
            'review' => $review,
        ], 201);
    }

    // PATCH /api/reviews/{review}
    public function update(
        UpdateReviewRequest $request,
        Review $review
    ) {
        $this->authorize('update', $review);

        $review->update($request->validated());

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review->fresh(),
        ]);
    }

    // DELETE /api/reviews/{review}
    public function destroy(
        Request $request,
        Review $review
    ) {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully.',
        ]);
    }
}