<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SQS ingest consumer
    |--------------------------------------------------------------------------
    |
    | Long-polls the queue for plain JSON messages (e.g. from Node SendMessage).
    | Run: php artisan observability:consume-sqs
    |
    */

    'sqs' => [
        'consumer_enabled' => filter_var(
            env('OBSERVABILITY_SQS_CONSUMER_ENABLED', false),
            FILTER_VALIDATE_BOOLEAN
        ),

        'queue_url' => env('OBSERVABILITY_SQS_QUEUE_URL'),

        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),

        'wait_time_seconds' => (int) env('OBSERVABILITY_SQS_WAIT_TIME_SECONDS', 20),

        'max_messages' => (int) env('OBSERVABILITY_SQS_MAX_MESSAGES', 10),
    ],

];
