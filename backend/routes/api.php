<?php

use App\Http\Controllers\Api\V1\IngestController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/ingest', [IngestController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/logs/{project_id}', [LogController::class, 'get_logs_by_project_id'])->middleware('auth:sanctum');
    Route::get('/logs/{project_id}/{event_id}', [LogController::class, 'get_log_by_project_id_event_id'])->middleware('auth:sanctum');
});

Route::post('/login', [LoginController::class, 'authenticate']);