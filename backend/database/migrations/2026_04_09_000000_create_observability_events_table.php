<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observability_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('schema_version', 32);
            $table->string('event_type');
            $table->string('severity', 32);
            $table->timestamp('occurred_at');
            $table->string('project_id');
            $table->string('user_id')->nullable();
            $table->string('service_type')->nullable();
            $table->string('request_id')->nullable();
            $table->unsignedInteger('tokens_used')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->boolean('has_correction')->default(false);
            $table->boolean('has_recommended')->default(false);
            $table->boolean('has_appointment')->default(false);
            $table->string('provider', 64)->nullable();
            $table->string('model')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_events');
    }
};
