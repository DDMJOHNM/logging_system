<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Observability\PersistObservabilityEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngestController extends Controller
{
    public function __construct(
        private PersistObservabilityEvent $persistObservabilityEvent,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate(PersistObservabilityEvent::rules());

        $event = $this->persistObservabilityEvent->persist($request->all());

        return response()->json([
            'ok' => true,
            'id' => $event->id,
            'event_id' => $event->event_id,
        ], 201);
    }
}
