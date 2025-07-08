<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Models\Category;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function adminStats()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $stats = [
            // Basic counts
            'total_reports' => Report::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_categories' => Category::where('is_active', true)->count(),

            // Report status counts
            'pending_reports' => Report::where('status', 'pending')->count(),
            'verified_reports' => Report::where('status', 'verified')->count(),
            'in_progress_reports' => Report::where('status', 'in_progress')->count(),
            'completed_reports' => Report::where('status', 'completed')->count(),
            'rejected_reports' => Report::where('status', 'rejected')->count(),

            // Today's data
            'reports_today' => Report::whereDate('created_at', Carbon::today())->count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),

            // This week's data
            'reports_this_week' => Report::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),

            // This month's data
            'reports_this_month' => Report::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),

            // Rating statistics
            'average_rating' => round(Rating::avg('rating'), 2),
            'total_ratings' => Rating::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get reports by category
     */
    public function reportsByCategory()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $reportsByCategory = Category::withCount('reports')
            ->get()
            ->map(function ($category) {
                return [
                    'category' => $category->name,
                    'count' => $category->reports_count,
                    'color' => $category->color
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reportsByCategory
        ]);
    }

    /**
     * Get reports trend (last 7 days)
     */
    public function reportsTrend()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Report::whereDate('created_at', $date)->count();

            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'count' => $count
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $trend
        ]);
    }

    /**
     * Get monthly reports statistics
     */
    public function monthlyReports()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $monthlyData = Report::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month)->format('M'),
                    'count' => $item->count
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $monthlyData
        ]);
    }

    /**
     * Get recent reports
     */
    public function recentReports()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $recentReports = Report::with(['user:id,name', 'category:id,name'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'status' => $report->status,
                    'category' => $report->category->name,
                    'user' => $report->user->name,
                    'created_at' => $report->created_at->format('d M Y H:i'),
                    'location' => $report->address
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $recentReports
        ]);
    }

    /**
     * Get user dashboard (for regular users)
     */
    public function userStats()
    {
        $user = Auth::user();

        // Statistik laporan berdasarkan user
        $stats = [
            'total_reports' => Report::where('user_id', $user->id)->count(),
            'pending_reports' => Report::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_reports' => Report::where('user_id', $user->id)->where('status', 'completed')->count(),
            'in_progress_reports' => Report::where('user_id', $user->id)->where('status', 'in_progress')->count(),
        ];

        // Data pemeliharaan yang dibuat admin, ditampilkan untuk semua user
        $pemeliharaan = \App\Models\Pemeliharaan::with('laporan')
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_fasilitas' => $item->nama_fasilitas,
                    'deskripsi' => $item->deskripsi,
                    'tgl_pemeliharaan' => $item->tgl_pemeliharaan,
                    'catatan' => $item->catatan,
                    'foto' => $item->foto ?? null, // Tambahkan jika kamu punya kolom foto
                    'laporan_judul' => $item->laporan->title ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'nama' => $user->nama,
                    'photo' => $user->photo ?? null, // pastikan kolom ini tersedia di tabel users
                ],
                'stats' => $stats,
                'pemeliharaan' => $pemeliharaan
            ]
        ]);
    }



    /**
     * Get user's recent reports
     */
    public function userRecentReports()
    {
        $recentReports = Report::where('user_id', Auth::id())
            ->with('category:id,name')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'status' => $report->status,
                    'category' => $report->category->name,
                    'created_at' => $report->created_at->format('d M Y'),
                    'can_rate' => $report->status === 'completed' && !$report->rating
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $recentReports
        ]);
    }

    /**
     * Get priority areas (locations with most reports)
     */
    public function priorityAreas()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $priorityAreas = Report::select('village', DB::raw('COUNT(*) as report_count'))
            ->whereNotNull('village')
            ->groupBy('village')
            ->orderBy('report_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $priorityAreas
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performanceMetrics()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $completedReports = Report::where('status', 'completed')->get();

        $avgResolutionTime = 0;
        if ($completedReports->count() > 0) {
            $totalDays = $completedReports->sum(function ($report) {
                return $report->created_at->diffInDays($report->updated_at);
            });
            $avgResolutionTime = round($totalDays / $completedReports->count(), 1);
        }

        $metrics = [
            'completion_rate' => Report::count() > 0 ?
                round((Report::where('status', 'completed')->count() / Report::count()) * 100, 1) : 0,
            'avg_resolution_time_days' => $avgResolutionTime,
            'customer_satisfaction' => round(Rating::avg('rating'), 2),
            'response_rate' => Report::count() > 0 ?
                round(((Report::count() - Report::where('status', 'pending')->count()) / Report::count()) * 100, 1) : 0,
            'total_pemeliharaan' => \App\Models\Pemeliharaan::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics
        ]);
    }
}
