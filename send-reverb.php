#!/usr/bin/env php
<?php

/**
 * Script para testar envio de eventos para o Reverb
 * 
 * Uso:
 *   php send-reverb.php [evento] [canal] [dados] [app_id] [app_key] [app_secret] [host] [port] [scheme]
 * 
 * Exemplos:
 *   php send-reverb.php
 *   php send-reverb.php ChatMessageSent chat-room.test
 *   php send-reverb.php ChatMessageSent chat-room.test '{"message":"Hello World"}'
 */

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

$event = $argv[1] ?? 'ChatMessageSent';
$channel = $argv[2] ?? 'chat-room.test'; // Changed from 'private-test' to 'chat-room.test'
$data = $argv[3] ?? '{"message":"Hello from the console!"}';
$appId = $argv[4] ?? $_ENV['REVERB_APP_ID'] ?? 'reverb';
$appKey = $argv[5] ?? $_ENV['REVERB_APP_KEY'] ?? 'reverb';
$appSecret = $argv[6] ?? $_ENV['REVERB_APP_SECRET'] ?? 'reverb';
$host = $argv[7] ?? $_ENV['REVERB_HOST'] ?? '127.0.0.1';
$port = $argv[8] ?? $_ENV['REVERB_PORT'] ?? '8080';
$scheme = $argv[9] ?? $_ENV['REVERB_SCHEME'] ?? 'http';

if (! is_array($decodedData = json_decode($data, true))) {
    $decodedData = ['message' => $data];
}

$path = '/apps/'.$appId.'/events?channel_name='.urlencode($channel).'&name='.urlencode($event).'&data='.urlencode($data);

$signature = hash_hmac('sha256', $path, $appSecret);

$response = (new Client)->post($scheme.'://'.$host.':'.$port.$path.'&auth_signature='.$signature, [
    'headers' => [
        'Authorization' => 'Bearer '.$appKey,
        'Content-Type' => 'application/json',
    ],
]);

echo '> Event dispatched successfully.'.PHP_EOL;
