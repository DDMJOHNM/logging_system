<?php

use App\Http\Controllers\Api\V1\IngestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/ingest', [IngestController::class, 'store']);
});
