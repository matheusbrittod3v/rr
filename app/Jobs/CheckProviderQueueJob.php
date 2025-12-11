<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\FilaPrestador;
use App\Services\IdempotencyService;
use Illuminate\Support\Facades\Log;

class CheckProviderQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $idempotencyKey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate idempotency key for this job execution
        $this->idempotencyKey = IdempotencyService::createKey('check_provider_queue', [
            'timestamp' => now()->timestamp
        ]);

        // Check if this job has already been processed recently
        if (IdempotencyService::hasBeenProcessed($this->idempotencyKey, 'check_provider_queue')) {
            Log::info('Idempotency check: CheckProviderQueueJob already processed', [
                'idempotency_key' => $this->idempotencyKey
            ]);
            return;
        }

        try {
            // Get all active providers (users marked as providers)
            $providers = User::where('is_provider', true)->get();
            
            // Get current providers in queue
            $queuedProviders = FilaPrestador::pluck('user_id')->toArray();
            
            // Add new providers to queue maintaining existing positions
            $position = FilaPrestador::max('position') + 1;
            
            foreach ($providers as $provider) {
                // If provider is not already in queue, add them
                if (!in_array($provider->id, $queuedProviders)) {
                    FilaPrestador::create([
                        'user_id' => $provider->id,
                        'position' => $position++
                    ]);
                }
            }
            
            Log::info('Provider queue checked and updated.');
            
            // Mark this operation as processed
            IdempotencyService::markAsProcessed($this->idempotencyKey, 'check_provider_queue', [
                'provider_count' => $providers->count(),
                'queued_provider_count' => count($queuedProviders)
            ]);
        } catch (\Exception $e) {
            Log::error("Error in CheckProviderQueueJob: {$e->getMessage()}", [
                'exception' => $e
            ]);
            
            // Even if there's an exception, mark the job as processed to prevent duplicate executions
            try {
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'check_provider_queue', [
                    'error_message' => $e->getMessage()
                ]);
            } catch (\Exception $markingException) {
                Log::error("Failed to mark idempotency key as processed in CheckProviderQueueJob", [
                    'marking_exception' => $markingException->getMessage()
                ]);
            }
        }
    }
}