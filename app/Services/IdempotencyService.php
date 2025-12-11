<?php

namespace App\Services;

use App\Models\IdempotencyKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IdempotencyService
{
    /**
     * Check if an operation with the given key has already been processed
     *
     * @param string $key
     * @param string $operationType
     * @return bool
     */
    public static function hasBeenProcessed(string $key, string $operationType): bool
    {
        return IdempotencyKey::where('key', $key)
            ->where('operation_type', $operationType)
            ->whereNotNull('processed_at')
            ->exists();
    }

    /**
     * Mark an operation as processed
     *
     * @param string $key
     * @param string $operationType
     * @param mixed $payload
     * @return void
     */
    public static function markAsProcessed(string $key, string $operationType, $payload = null): void
    {
        try {
            // First try to update existing record
            $affected = IdempotencyKey::where('key', $key)
                ->where('operation_type', $operationType)
                ->update([
                    'payload' => is_array($payload) || is_object($payload) ? json_encode($payload) : $payload,
                    'processed_at' => now(),
                    'updated_at' => now()
                ]);
            
            // If no records were updated, create a new one
            if ($affected === 0) {
                try {
                    IdempotencyKey::create([
                        'key' => $key,
                        'operation_type' => $operationType,
                        'payload' => is_array($payload) || is_object($payload) ? json_encode($payload) : $payload,
                        'processed_at' => now()
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If there's still a unique constraint violation, it means another process
                    // created the record between our check and insert. This is fine, we can ignore it.
                    if (strpos($e->getMessage(), 'unique constraint') !== false) {
                        Log::info('Idempotency key already exists, ignoring duplicate insert', [
                            'key' => $key,
                            'operation_type' => $operationType
                        ]);
                    } else {
                        // Re-throw if it's a different kind of error
                        throw $e;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in IdempotencyService::markAsProcessed', [
                'key' => $key,
                'operation_type' => $operationType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw the exception so calling code knows something went wrong
            throw $e;
        }
    }

    /**
     * Create and store an idempotency key for an operation
     *
     * @param string $operationType
     * @param array $identifiers
     * @return string
     */
    public static function createKey(string $operationType, array $identifiers): string
    {
        // Create a unique key based on operation type and identifiers
        $identifierString = implode('|', $identifiers);
        return hash('sha256', $operationType . '|' . $identifierString . '|' . microtime(true));
    }

    /**
     * Execute an operation with idempotency protection
     *
     * @param string $key
     * @param string $operationType
     * @param callable $operation
     * @param mixed $payload
     * @return mixed
     */
    public static function executeOnce(string $key, string $operationType, callable $operation, $payload = null)
    {
        // Check if already processed
        if (self::hasBeenProcessed($key, $operationType)) {
            Log::info('Idempotency check: Operation already processed', [
                'key' => $key,
                'operation_type' => $operationType
            ]);
            return null; // or return a specific response indicating it was already processed
        }

        try {
            // Execute the operation
            $result = $operation();
            
            // Mark as processed
            self::markAsProcessed($key, $operationType, $payload);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('IdempotencyService: Error executing operation', [
                'key' => $key,
                'operation_type' => $operationType,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}