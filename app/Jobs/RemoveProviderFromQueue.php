<?php

namespace App\Jobs;

use App\Models\FilaPrestador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveProviderFromQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Removing provider {$this->userId} from queue due to deactivation.");

        $entry = FilaPrestador::where('user_id', $this->userId)->first();

        if ($entry) {
            $entry->delete();
            Log::info("Provider {$this->userId} removed from queue successfully.");
        } else {
            Log::info("Provider {$this->userId} was not found in the queue.");
        }
    }
}
