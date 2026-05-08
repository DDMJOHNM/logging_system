<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngestTest extends TestCase
{
    use RefreshDatabase;

    public function test_ingest_persists_event(): void
    {
        $payload = [
            'schema_version' => '1',
            'event_id' => '550e8400-e29b-41d4-a716-446655440000',
            'event_type' => 'open_api.request',
            'severity' => 'info',
            'occurred_at' => '2026-01-01T12:00:00Z',
            'project_id' => 'proj-1',
            'extra' => 'ignored_for_columns_but_in_payload',
        ];

        $response = $this->postJson('/api/v1/ingest', $payload);

        $response->assertCreated()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('event_id', '550e8400-e29b-41d4-a716-446655440000');

        $this->assertDatabaseHas('observability_events', [
            'event_id' => '550e8400-e29b-41d4-a716-446655440000',
            'project_id' => 'proj-1',
            'event_type' => 'open_api.request',
        ]);
    }
}
