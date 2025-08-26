<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresetController;

Route::middleware('auth')->group(function () {
    Route::get('/presets', [PresetController::class, 'index']);
    Route::post('/presets', [PresetController::class, 'store']);
    Route::put('/presets/{preset}', [PresetController::class, 'update']);
    Route::delete('/presets/{preset}', [PresetController::class, 'destroy']);

    // Dashboard API endpoints
    Route::get('/dashboard/metrics', function () {
        return response()->json([
            'total_patients' => \App\Models\Patient::count(),
            'new_patients_this_month' => \App\Models\Patient::whereMonth('created_at', now()->month)->count(),
            'follow_ups_this_month' => \App\Models\FollowUp::whereMonth('created_at', now()->month)->count(),
            'total_revenue' => \App\Models\FollowUp::sum('amount_paid'),
        ]);
    });

    Route::get('/queue/count', function () {
        return response()->json([
            'count' => \App\Models\Queue::whereDate('in_queue_at', today())->count()
        ]);
    });
});
