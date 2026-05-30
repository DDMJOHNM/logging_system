<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Observability\PersistObservabilityEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IngestController extends Controller
{
    //testing end point only
    public function __construct(
        private PersistObservabilityEvent $persistObservabilityEvent,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate(PersistObservabilityEvent::rules());

        $event = $this->persistObservabilityEvent->persist($request->all());
        Cache::put($event->event_id, $event->toArray(), 6000);

        return response()->json([
            'ok' => true,
            'id' => $event->id,
            'event_id' => $event->event_id,
        ], 201);
    }
}
