<?php

namespace App\Http\Controllers;

use App\Models\ObservabilityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class LogController extends Controller
{
    /**
     * @var list<string>
     */
    private const ALLOWED_ORDER_COLUMNS = [
        'id',
        'occurred_at',
        'created_at',
        'event_id',
        'event_type',
        'severity',
        'project_id',
        'tokens_used',
        'latency_ms',
    ];

    public function get_logs_by_project_id(Request $request, string $project_id): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'occurred_at');
        if (! is_string($orderBy) || ! in_array($orderBy, self::ALLOWED_ORDER_COLUMNS, true)) {
            $orderBy = 'occurred_at';
        }

        $direction = strtolower((string) $request->query('orderDirection', 'desc'));
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }
        
        $page = max(1, (int) $request->query('page', 1));

        $perPage = max(1, min(100, (int) $request->query('perPage', 10)));

        $query = ObservabilityEvent::query()
            ->where('project_id', $project_id);

        if ($request->filled('severity')) {
            $severity = $request->query('severity');
            if (is_string($severity) && strlen($severity) <= 32) {
                $query->where('severity', $severity);
            }
        }

        $logs = $query
            ->orderBy($orderBy, $direction)
            ->paginate($perPage, ['*'], 'page', $page);            

        //When using get if the logs are already in the cache get them from the cache instead of the database    
        // foreach ($logs as $log) {
        //     $cachedLog = Cache::get('log_'.$log->event_id, $log->toArray(), 6000);
        //     if ($cachedLog) {
        //         $logs->push($cachedLog);
        //     }
        // }
        //Cache::put('logs_'.$project_id.'_'.$page.'_'.$perPage.'_'.$orderBy.'_'.$direction, $logs->toArray(), 6000);
        // foreach ($logs as $log) {
        //     Cache::get('log_'.$log->event_id, $log->toArray(), 6000);
        // }

        return response()->json([
            'logs' => $logs,
        ]);
    }

    public function get_log_by_project_id_event_id(string $project_id, string $event_id): JsonResponse
    {
        if (! Str::isUuid($event_id)) {
            return response()->json(['message' => 'Invalid event_id.'], 422);
        }

        $log = ObservabilityEvent::query()
            ->where('project_id', $project_id)
            ->where('event_id', $event_id)
            ->first();

        if ($log === null) {
            return response()->json(['message' => 'Log not found.'], 404);
        }

        return response()->json([
            'log' => $log,
        ]);
    }
}
