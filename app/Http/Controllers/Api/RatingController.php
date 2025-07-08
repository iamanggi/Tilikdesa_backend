<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * Submit rating for completed report
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|exists:reports,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if report exists and belongs to user
        $report = Report::where('id', $request->report_id)
                       ->where('user_id', Auth::id())
                       ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found or access denied'
            ], 404);
        }

        // Check if report is completed
        if ($report->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only rate completed reports'
            ], 400);
        }

        // Check if already rated
        $existingRating = Rating::where('report_id', $request->report_id)
                               ->where('user_id', Auth::id())
                               ->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'You have already rated this report'
            ], 400);
        }

        try {
            $rating = Rating::create([
                'report_id' => $request->report_id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => $rating
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating'
            ], 500);
        }
    }

    /**
     * Update existing rating
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $rating = Rating::where('id', $id)
                       ->where('user_id', Auth::id())
                       ->first();

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Rating not found or access denied'
            ], 404);
        }

        try {
            $rating->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
                'data' => $rating->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update rating'
            ], 500);
        }
    }

    /**
     * Get ratings for a report (admin only)
     */
    public function show($reportId)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $ratings = Rating::where('report_id', $reportId)
                        ->with('user:id,name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $ratings
        ]);
    }

    /**
     * Get rating statistics (admin only)
     */
    public function statistics()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $stats = [
            'total_ratings' => Rating::count(),
            'average_rating' => round(Rating::avg('rating'), 2),
            'rating_distribution' => [
                '5_star' => Rating::where('rating', 5)->count(),
                '4_star' => Rating::where('rating', 4)->count(),
                '3_star' => Rating::where('rating', 3)->count(),
                '2_star' => Rating::where('rating', 2)->count(),
                '1_star' => Rating::where('rating', 1)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Delete rating
     */
    public function destroy($id)
    {
        $rating = Rating::where('id', $id)
                       ->where('user_id', Auth::id())
                       ->first();

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Rating not found or access denied'
            ], 404);
        }

        try {
            $rating->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rating deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rating'
            ], 500);
        }
    }
}