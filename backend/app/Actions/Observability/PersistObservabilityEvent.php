<?php

namespace App\Actions\Observability;

use App\Models\ObservabilityEvent;
use Illuminate\Support\Facades\Validator;

class PersistObservabilityEvent
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
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
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function persist(array $input): ObservabilityEvent
    {
        $validated = Validator::make($input, self::rules())->validate();

        return ObservabilityEvent::create([
            ...$validated,
            'payload' => $input,
        ]);
    }
}
