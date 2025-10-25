<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Import all your new controllers ---
use App\Http\Controllers\CameraController;
use App\Http\Controllers\StatusLabelController;
use App\Http\Controllers\StatusHistoryController;
use App\Http\Controllers\RepairLogController;
// ... (you may have other existing controller imports here too)

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- Add these lines to create the API endpoints for your new resources ---
// This single line creates GET, POST, PUT, DELETE, etc. routes for cameras.
Route::apiResource('cameras', CameraController::class);

// Creates routes for status-labels (e.g., GET /api/status-labels)
Route::apiResource('status-labels', StatusLabelController::class);

// Creates routes for status-histories
Route::apiResource('status-histories', StatusHistoryController::class);

// Creates routes for repair-logs
Route::apiResource('repair-logs', RepairLogController::class);

// ... (you may have other existing routes here, leave them as they are)
