<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ServiceDataProcessor
{
    protected $batchSize;
    protected $filePath;
    protected $parallelProcessing;
    protected $maxProcesses;

    public function __construct($filePath, $batchSize = 1000, $parallelProcessing = false, $maxProcesses = 4)
    {
        $this->filePath = $filePath;
        $this->batchSize = $batchSize;
        $this->parallelProcessing = $parallelProcessing;
        $this->maxProcesses = $maxProcesses;
    }

    /**
     * Process the service data with optimization
     */
    public function process(): bool
    {
        // Check if file exists
        if (!File::exists($this->filePath)) {
            Log::error("JSON file not found at: " . $this->filePath);
            return false;
        }

        // Read and decode JSON
        $jsonData = json_decode(File::get($this->filePath), true);

        // Check if JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Invalid JSON format: " . json_last_error_msg());
            return false;
        }

        // Process the service data
        if (isset($jsonData['SUCCESS'][0]['LIST'])) {
            return $this->processServiceData($jsonData['SUCCESS'][0]['LIST']);
        } else {
            Log::error("Invalid JSON structure. Expected 'SUCCESS' array with 'LIST' key.");
            return false;
        }
    }

    /**
     * Process the service data and insert into database using bulk operations
     */
    private function processServiceData($serviceList): bool
    {
        try {
            // Disable foreign key constraints for faster inserts
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Truncate tables first for fresh data
            DB::table('service_requirements')->truncate();
            DB::table('service_custom_fields')->truncate();
            DB::table('services')->truncate();
            DB::table('service_groups')->truncate();
            
            // Re-enable foreign key constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            $serviceGroupsData = [];
            $servicesData = [];
            $customFieldsData = [];
            $requirementsData = [];
            
            $groupMap = []; // To map group names to IDs
            $serviceMap = []; // To map service keys to IDs
            
            $currentTime = now();
            $timestamp = $currentTime->format('Y-m-d H:i:s');
            
            // First, collect all service groups
            foreach ($serviceList as $groupName => $groupData) {
                $serviceGroupsData[] = [
                    'group_name' => $groupName,
                    'group_type' => $groupData['GROUPTYPE'] ?? null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
            
            // Bulk insert service groups
            $this->bulkInsert('service_groups', $serviceGroupsData);
            
            // Get all service groups with their IDs
            $groups = DB::table('service_groups')->get(['id', 'group_name']);
            foreach ($groups as $group) {
                $groupMap[$group->group_name] = $group->id;
            }
            
            // Collect all services data
            foreach ($serviceList as $groupName => $groupData) {
                $groupId = $groupMap[$groupName] ?? null;
                
                if (!$groupId) {
                    continue;
                }
                
                if (isset($groupData['SERVICES']) && is_array($groupData['SERVICES'])) {
                    foreach ($groupData['SERVICES'] as $serviceKey => $serviceData) {
                        $servicesData[] = [
                            'service_group_id' => $groupId,
                            'service_id' => $serviceKey,
                            'service_type' => $serviceData['SERVICETYPE'] ?? '',
                            'service_name' => $serviceData['SERVICENAME'] ?? '',
                            'qnt' => $serviceData['QNT'] ?? '0',
                            'server' => $serviceData['SERVER'] ?? '0',
                            'min_qnt' => $serviceData['MINQNT'] ?? '0',
                            'max_qnt' => $serviceData['MAXQNT'] ?? '0',
                            'credit' => $serviceData['CREDIT'] ?? '0',
                            'time' => $serviceData['TIME'] ?? '',
                            'info' => $serviceData['INFO'] ?? null,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }
                }
            }
            
            // Process services in batches
            $this->processInBatches('services', $servicesData);
            
            // Get all services with their IDs and service_id (key)
            $services = DB::table('services')->get(['id', 'service_id']);
            foreach ($services as $service) {
                $serviceMap[$service->service_id] = $service->id;
            }
            
            // Collect custom fields and requirements
            foreach ($serviceList as $groupName => $groupData) {
                if (isset($groupData['SERVICES']) && is_array($groupData['SERVICES'])) {
                    foreach ($groupData['SERVICES'] as $serviceKey => $serviceData) {
                        $serviceId = $serviceMap[$serviceKey] ?? null;
                        
                        if (!$serviceId) {
                            continue;
                        }
                        
                        // Process custom field if it exists
                        if (isset($serviceData['CUSTOM']) && is_array($serviceData['CUSTOM'])) {
                            $customData = $serviceData['CUSTOM'];
                            $customFieldsData[] = [
                                'service_id' => $serviceId,
                                'custom_name' => $customData['customname'] ?? null,
                                'custom_info' => $customData['custominfo'] ?? null,
                                'custom_len' => $customData['customlen'] ?? null,
                                'max_length' => $customData['maxlength'] ?? null,
                                'regex' => $customData['regex'] ?? null,
                                'is_alpha' => $customData['isalpha'] ?? null,
                                'created_at' => $timestamp,
                                'updated_at' => $timestamp,
                            ];
                        }
                        
                        // Process requirements if they exist
                        if (isset($serviceData['Requires.Custom'])) {
                            $requirements = $serviceData['Requires.Custom'];
                            
                            // Handle case where requirements is a single item (not array)
                            if (!is_array($requirements)) {
                                $requirements = [$requirements];
                            }
                            
                            // Handle case where requirements is an associative array
                            if (is_array($requirements) && !isset($requirements[0]) && count($requirements) > 0) {
                                $requirements = [$requirements];
                            }
                            
                            // Process each requirement
                            if (is_array($requirements)) {
                                foreach ($requirements as $requirement) {
                                    if (is_array($requirement)) {
                                        $requirementsData[] = [
                                            'service_id' => $serviceId,
                                            'type' => $requirement['type'] ?? null,
                                            'field_name' => $requirement['fieldname'] ?? '',
                                            'field_type' => $requirement['fieldtype'] ?? '',
                                            'description' => $requirement['description'] ?? null,
                                            'field_options' => $requirement['fieldoptions'] ?? null,
                                            'regex' => $requirement['regexpr'] ?? null,
                                            'admin_only' => $requirement['adminonly'] ?? null,
                                            'required' => isset($requirement['required']) && $requirement['required'] === 'on' ? 1 : 0,
                                            'created_at' => $timestamp,
                                            'updated_at' => $timestamp,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Process custom fields in batches
            if (!empty($customFieldsData)) {
                $this->processInBatches('service_custom_fields', $customFieldsData);
            }
            
            // Process requirements in batches
            if (!empty($requirementsData)) {
                $this->processInBatches('service_requirements', $requirementsData);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Error processing service data: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process data in batches
     */
    private function processInBatches($table, $data): void
    {
        $batches = array_chunk($data, $this->batchSize);
        
        foreach ($batches as $batch) {
            $this->bulkInsert($table, $batch);
        }
    }
    
    /**
     * Bulk insert with optimization
     */
    private function bulkInsert($table, $data): void
    {
        if (empty($data)) {
            return;
        }
        
        // Use the built-in insert method for better compatibility
        DB::table($table)->insert($data);
    }
}