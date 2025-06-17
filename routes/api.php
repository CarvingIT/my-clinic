<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresetController;

Route::middleware('auth')->group(function () {
    Route::get('/presets', [PresetController::class, 'index']);
    Route::post('/presets', [PresetController::class, 'store']);
    Route::put('/presets/{preset}', [PresetController::class, 'update']);
    Route::delete('/presets/{preset}', [PresetController::class, 'destroy']);
});
