<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PemeliharaanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->get('/dashboard/pemeliharaan', [DashboardController::class, 'allPemeliharaan']);


// Public categories (for dropdown in registration/report creation)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/search', [CategoryController::class, 'search']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // Dashboard routes
    Route::get('/dashboard/user', [DashboardController::class, 'userStats']);
    Route::get('/dashboard/user/recent-reports', [DashboardController::class, 'userRecentReports']);
    
    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::post('/', [ReportController::class, 'store']);
        Route::get('/my-reports', [ReportController::class, 'myReports']);
        Route::get('/search', [ReportController::class, 'search']);
        Route::get('/filter', [ReportController::class, 'filter']);
        Route::get('/map-data', [ReportController::class, 'mapData']);
        Route::get('/{id}', [ReportController::class, 'show']);
        Route::put('/{id}', [ReportController::class, 'update']);
        Route::delete('/{id}', [ReportController::class, 'destroy']);
        
        // Report images
        Route::post('/{id}/images', [ReportController::class, 'uploadImages']);
        Route::delete('/{id}/images/{imageId}', [ReportController::class, 'deleteImage']);
    });
    
    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
    
    // Ratings routes
    Route::prefix('ratings')->group(function () {
        Route::post('/', [RatingController::class, 'store']);
        Route::put('/{id}', [RatingController::class, 'update']);
        Route::delete('/{id}', [RatingController::class, 'destroy']);
    });
    
    // Admin only routes
    Route::middleware('admin')->group(function () {
        
        // Admin dashboard
        Route::prefix('dashboard/admin')->group(function () {
            Route::get('/stats', [DashboardController::class, 'adminStats']);
            Route::get('/reports-by-category', [DashboardController::class, 'reportsByCategory']);
            Route::get('/reports-trend', [DashboardController::class, 'reportsTrend']);
            Route::get('/monthly-reports', [DashboardController::class, 'monthlyReports']);
            Route::get('/recent-reports', [DashboardController::class, 'recentReports']);
            Route::get('/priority-areas', [DashboardController::class, 'priorityAreas']);
            Route::get('/performance-metrics', [DashboardController::class, 'performanceMetrics']);
        });
        
        // Admin report management
        Route::prefix('admin/reports')->group(function () {
            // Route::get('/', [ReportController::class, 'adminIndex']);
            Route::put('/{id}/status', [ReportController::class, 'updateStatus']);
            Route::put('/{id}/verify', [ReportController::class, 'verifyReport']);
            Route::put('/{id}/reject', [ReportController::class, 'rejectReport']);
            Route::get('/statistics', [ReportController::class, 'statistics']);
            Route::get('/export', [ReportController::class, 'export']);
        });
        
        // Admin category management
        Route::prefix('admin/categories')->group(function () {
            Route::get('/', [CategoryController::class, 'indexAll']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('/with-counts', [CategoryController::class, 'withReportCounts']);
            Route::get('/statistics', [CategoryController::class, 'statistics']);
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
            Route::put('/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);
        });
        
        // Admin user management
        Route::prefix('admin/users')->group(function () {
            Route::get('/', [AuthController::class, 'adminGetUsers']);
            Route::get('/{id}', [AuthController::class, 'adminGetUser']);
            Route::put('/{id}/toggle-status', [AuthController::class, 'adminToggleUserStatus']);
            Route::get('/statistics', [AuthController::class, 'adminUserStatistics']);
        });
        
        // Admin ratings management
        Route::prefix('admin/ratings')->group(function () {
            Route::get('/report/{reportId}', [RatingController::class, 'show']);
            Route::get('/statistics', [RatingController::class, 'statistics']);
        });
        
        // Admin notifications (send to users)
        Route::prefix('admin/notifications')->group(function () {
            Route::post('/broadcast', [NotificationController::class, 'broadcastNotification']);
            Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
        });
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/pemeliharaan')->group(function () {
    Route::get('/', [PemeliharaanController::class, 'index']);
    Route::get('/create', [PemeliharaanController::class, 'create']);
    Route::post('/', [PemeliharaanController::class, 'store']);
    Route::get('/{pemeliharaan}', [PemeliharaanController::class, 'show']);
    Route::get('/{pemeliharaan}/edit', [PemeliharaanController::class, 'edit']);
    Route::put('/{pemeliharaan}', [PemeliharaanController::class, 'update']);
    Route::delete('/{pemeliharaan}', [PemeliharaanController::class, 'destroy']);
});



// Fallback for undefined routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});