<?php

namespace App\Providers;

use Aws\Sqs\SqsClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SqsClient::class, function (): SqsClient {
            return new SqsClient([
                'version' => 'latest',
                'region' => config('observability.sqs.region'),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
