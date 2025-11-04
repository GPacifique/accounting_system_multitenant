<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API routes with authentication and rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Add additional API routes here with proper authorization
    // Example:
    // Route::apiResource('projects', ProjectApiController::class);
    // Route::apiResource('expenses', ExpenseApiController::class);
});

// Public API routes (if needed) with aggressive rate limiting
Route::middleware(['throttle:public_api'])->group(function () {
    // Add public API endpoints here if needed
    // Example: Route::get('/status', StatusController::class);
});