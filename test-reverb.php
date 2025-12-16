#!/usr/bin/env php
<?php

/**
 * Script para testar conexão e envio de eventos para o Reverb
 * 
 * Uso:
 *   php test-reverb.php [host] [port] [scheme] [app_key] [app_id] [app_secret]
 * 
 * Exemplos:
 *   php test-reverb.php
 *   php test-reverb.php localhost 8080 http your-app-key your-app-id your-app-secret
 */

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector as RatchetConnector;
use React\EventLoop\Loop;
use React\Socket\Connector as ReactConnector;

$host = $argv[1] ?? $_ENV['REVERB_HOST'] ?? '127.0.0.1';
$port = $argv[2] ?? $_ENV['REVERB_PORT'] ?? '8080';
$scheme = $argv[3] ?? $_ENV['REVERB_SCHEME'] ?? 'http';
$appKey = $argv[4] ?? $_ENV['REVERB_APP_KEY'] ?? 'reverb';
$appId = $argv[5] ?? $_ENV['REVERB_APP_ID'] ?? 'reverb';
$appSecret = $argv[6] ?? $_ENV['REVERB_APP_SECRET'] ?? 'reverb';

echo "> Testing Reverb connection...\n";
echo "> Host: $host:$port\n";
echo "> Scheme: $scheme\n";
echo "> App Key: $appKey\n";
echo "> App ID: $appId\n";

// Test HTTP connection
try {
    $httpClient = new Client;
    $response = $httpClient->get("$scheme://$host:$port/apps/$appId", [
        'headers' => [
            'Authorization' => 'Bearer '.$appKey,
        ],
    ]);

    echo "> ✅ HTTP connection successful\n";
    echo "> Response: ".$response->getBody()->getContents()."\n\n";
} catch (Exception $e) {
    echo "> ❌ HTTP connection failed: ".$e->getMessage()."\n\n";
}

// Test WebSocket connection
echo "> Testing WebSocket connection...\n";

$loop = Loop::get();

$connector = new RatchetConnector($loop, new ReactConnector($loop));

$connector("$scheme://$host:$port/app/$appKey", [
    'Origin' => "$scheme://$host:$port",
])->then(function (WebSocket $conn) use ($loop) {
    echo "> ✅ WebSocket connection established\n";
    
    $conn->on('message', function ($message) {
        echo "> Received message: $message\n";
        
        $data = json_decode($message, true);
        
        if (isset($data['event']) && $data['event'] === 'pusher:connection_established') {
            echo "> ✅ Pusher connection established\n";
            
            // Subscribe to a test channel
            $conn->send(json_encode([
                'event' => 'pusher:subscribe',
                'data' => [
                    'channel' => 'chat-room.test',
                ],
            ]));
        }
        
        if (isset($data['event']) && $data['event'] === 'pusher_internal:subscription_succeeded') {
            echo "> ✅ Successfully subscribed to channel\n";
            
            // Send a test event
            $conn->send(json_encode([
                'event' => 'client-test-event',
                'channel' => 'chat-room.test',
                'data' => json_encode([
                    'message' => 'Hello from WebSocket test!',
                ]),
            ]));
        }
    });
    
    $conn->on('close', function ($code = null, $reason = null) {
        echo "> WebSocket connection closed: $code $reason\n";
    });
    
    $conn->on('error', function ($e) {
        echo "> WebSocket error: ".$e->getMessage()."\n";
    });
    
    // Send connection message
    $conn->send(json_encode([
        'event' => 'pusher:subscribe',
        'data' => [
            'auth' => '',
            'channel' => 'chat-room.test',
        ],
    ]));
    
}, function (Exception $e) use ($loop) {
    echo "> ❌ WebSocket connection failed: ".$e->getMessage()."\n";
    $loop->stop();
});

// Stop the loop after 10 seconds
$loop->addTimer(10, function () use ($loop) {
    echo "> Test completed\n";
    $loop->stop();
});

$loop->run();