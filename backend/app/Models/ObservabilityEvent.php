<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservabilityEvent extends Model
{
    protected $fillable = [
        'schema_version',
        'event_id',
        'event_type',
        'severity',
        'occurred_at',
        'project_id',
        'user_id',
        'service_type',
        'request_id',
        'tokens_used',
        'latency_ms',
        'has_correction',
        'has_recommended',
        'has_appointment',
        'provider',
        'model',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'has_correction' => 'boolean',
            'has_recommended' => 'boolean',
            'has_appointment' => 'boolean',
            'payload' => 'array',
        ];
    }
}
