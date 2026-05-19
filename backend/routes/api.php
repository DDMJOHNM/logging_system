<?php

use App\Http\Controllers\Api\V1\IngestController;
use App\Http\Controllers\LogController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/ingest', [IngestController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/logs/{project_id}', [LogController::class, 'get_logs_by_project_id'])->middleware('auth:sanctum');
    Route::get('/logs/{project_id}/{event_id}', [LogController::class, 'get_log_by_project_id_event_id'])->middleware('auth:sanctum');
});

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'token_name' => 'sometimes|string|max:255',
    ]);

    $user = User::query()->where('email', $credentials['email'])->first();

    if ($user === null || ! Hash::check($credentials['password'], $user->getAuthPassword())) {
        return response()->json(['message' => 'Invalid credentials.'], 422);
    }

    $name = $credentials['token_name'] ?? 'api-token';
    $token = $user->createToken($name);

    return response()->json(['token' => $token->plainTextToken]);
});