<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportStatusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'category', 'status', 'location']);

        if ($request->has('status_id') && $request->status_id != '') {
            $query->where('status_id', $request->status_id);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location_id') && $request->location_id != '') {
            $query->where('location_id', $request->location_id);
        }

        if ($request->user()->role === 'masyarakat') {
            $query->where('user_id', $request->user()->id);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'location_id' => 'required|exists:locations,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $filename = Str::uuid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('reports', $filename, 'public');
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $photoPath,
            'location_id' => $request->location_id,
            'report_date' => now(),
            'status_id' => 1, // default: status baru
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report created successfully',
            'data' => $report
        ], 201);
    }

    public function show($id)
    {
        $report = Report::with(['user', 'category', 'status', 'location'])
                        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }



    public function destroy(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if (!$report->canBeDeletedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete this report'
            ], 403);
        }

        if ($report->photo) {
            Storage::disk('public')->delete($report->photo);
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status_id' => 'required|exists:statuses,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $report = Report::findOrFail($id);

        ReportStatusUpdate::create([
            'report_id' => $report->id,
            'updated_by' => $request->user()->id,
            'old_status_id' => $report->status_id,
            'new_status_id' => $request->status_id,
            'notes' => $request->notes,
        ]);

        $report->update([
            'status_id' => $request->status_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $report->fresh()
        ]);
    }
}
