<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ObservabilityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'schema_version' => 'required|string|max:32',
            'event_id' => 'required|uuid',
            'event_type' => 'required|string|max:255',
            'severity' => 'required|string|max:32',
            'occurred_at' => 'required|date',
            'project_id' => 'required|string|max:255',
            'user_id' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'request_id' => 'nullable|string|max:255',
            'tokens_used' => 'nullable|integer|min:0',
            'latency_ms' => 'nullable|integer|min:0',
            'has_correction' => 'sometimes|boolean',
            'has_recommended' => 'sometimes|boolean',
            'has_appointment' => 'sometimes|boolean',
            'provider' => 'nullable|string|max:64',
            'model' => 'nullable|string|max:255',
        ]);

        $event = ObservabilityEvent::create([
            ...$validated,
            'payload' => $request->all(),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $event->id,
            'event_id' => $event->event_id,
        ], 201);
    }
}
