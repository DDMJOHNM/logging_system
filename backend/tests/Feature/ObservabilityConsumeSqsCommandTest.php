<?php

namespace Tests\Feature;

use Aws\Result;
use Aws\Sqs\SqsClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ObservabilityConsumeSqsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_consumes_valid_json_message_persists_and_deletes(): void
    {
        config([
            'observability.sqs.consumer_enabled' => true,
            'observability.sqs.queue_url' => 'https://sqs.us-east-1.amazonaws.com/123456789012/observability',
            'observability.sqs.wait_time_seconds' => 0,
            'observability.sqs.max_messages' => 10,
        ]);

        $payload = [
            'schema_version' => '1',
            'event_id' => '550e8400-e29b-41d4-a716-446655440000',
            'event_type' => 'open_api.request',
            'severity' => 'info',
            'occurred_at' => '2026-01-01T12:00:00Z',
            'project_id' => 'proj-sqs',
        ];

        $result = new Result([
            'Messages' => [
                [
                    'ReceiptHandle' => 'rh-test-1',
                    'Body' => json_encode($payload),
                ],
            ],
        ]);

        $mock = Mockery::mock(SqsClient::class);
        $mock->shouldReceive('receiveMessage')
            ->once()
            ->andReturn($result);
        $mock->shouldReceive('deleteMessage')
            ->once()
            ->with(Mockery::on(function (array $args): bool {
                return $args['ReceiptHandle'] === 'rh-test-1'
                    && str_contains((string) $args['QueueUrl'], 'observability');
            }));

        $this->app->instance(SqsClient::class, $mock);

        $this->artisan('observability:consume-sqs', ['--once' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('observability_events', [
            'event_id' => '550e8400-e29b-41d4-a716-446655440000',
            'project_id' => 'proj-sqs',
        ]);
    }
}
