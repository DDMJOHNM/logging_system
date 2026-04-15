<?php

namespace App\Console\Commands;

use App\Actions\Observability\PersistObservabilityEvent;
use Aws\Sqs\SqsClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use JsonException;
use Throwable;

class ObservabilityConsumeSqsCommand extends Command
{
    protected $signature = 'observability:consume-sqs
                            {--once : Receive at most one batch of messages then exit}';

    protected $description = 'Long-poll SQS for plain JSON observability events and persist them';

    private bool $running = true;

    public function __construct(
        private PersistObservabilityEvent $persistObservabilityEvent,
    ) {
        parent::__construct();
    }

    public function handle(SqsClient $sqs): int
    {
        if (! config('observability.sqs.consumer_enabled')) {
            $this->warn('OBSERVABILITY_SQS_CONSUMER_ENABLED is false; exiting.');

            return self::SUCCESS;
        }

        $queueUrl = config('observability.sqs.queue_url');
        if (! is_string($queueUrl) || $queueUrl === '') {
            $this->error('OBSERVABILITY_SQS_QUEUE_URL is not set.');

            return self::FAILURE;
        }

        $this->registerSignalHandlers();

        $waitSeconds = (int) config('observability.sqs.wait_time_seconds');
        $maxMessages = (int) config('observability.sqs.max_messages');

        $this->info('Observability SQS consumer started.');

        while ($this->running) {
            try {
                $result = $sqs->receiveMessage([
                    'QueueUrl' => $queueUrl,
                    'MaxNumberOfMessages' => max(1, min(10, $maxMessages)),
                    'WaitTimeSeconds' => max(0, min(20, $waitSeconds)),
                    'AttributeNames' => ['All'],
                ]);
            } catch (Throwable $e) {
                Log::error('SQS receiveMessage failed', ['exception' => $e]);
                $this->error($e->getMessage());
                sleep(5);

                continue;
            }

            $messages = $result->get('Messages') ?? [];
            foreach ($messages as $message) {
                $this->processMessage($sqs, $queueUrl, $message);
            }

            if ($this->option('once')) {
                break;
            }
        }

        $this->info('Observability SQS consumer stopped.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $message
     */
    private function processMessage(SqsClient $sqs, string $queueUrl, array $message): void
    {
        $receipt = $message['ReceiptHandle'] ?? null;
        if (! is_string($receipt) || $receipt === '') {
            return;
        }

        $body = $message['Body'] ?? '';
        if (! is_string($body)) {
            return;
        }

        try {
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::warning('SQS message body is not valid JSON; deleting', ['message' => $e->getMessage()]);
            $this->deleteMessage($sqs, $queueUrl, $receipt);

            return;
        }

        if (! is_array($data)) {
            Log::warning('SQS message JSON root must be an object; deleting');
            $this->deleteMessage($sqs, $queueUrl, $receipt);

            return;
        }

        try {
            $this->persistObservabilityEvent->persist($data);
        } catch (ValidationException $e) {
            Log::warning('SQS message failed validation; deleting', [
                'errors' => $e->errors(),
            ]);
            $this->deleteMessage($sqs, $queueUrl, $receipt);

            return;
        } catch (Throwable $e) {
            Log::error('Failed to persist observability event from SQS', ['exception' => $e]);

            return;
        }

        $this->deleteMessage($sqs, $queueUrl, $receipt);
    }

    private function deleteMessage(SqsClient $sqs, string $queueUrl, string $receiptHandle): void
    {
        try {
            $sqs->deleteMessage([
                'QueueUrl' => $queueUrl,
                'ReceiptHandle' => $receiptHandle,
            ]);
        } catch (Throwable $e) {
            Log::error('SQS deleteMessage failed', ['exception' => $e]);
        }
    }

    private function registerSignalHandlers(): void
    {
        if (! function_exists('pcntl_async_signals')) {
            return;
        }

        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, function (): void {
            $this->running = false;
        });
        pcntl_signal(SIGINT, function (): void {
            $this->running = false;
        });
    }
}
