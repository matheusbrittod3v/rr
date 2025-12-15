<?php

/**
 * TESTE DE CONEXÃO COM LARAVEL REVERB (PHP PURO)
 */

$REVERB_APP_ID     = '345678';
$REVERB_APP_KEY    = 'sdfghjkhgfdfghjkhgfghjk';
$REVERB_APP_SECRET = 'dfghjkrtyuijhgfghjklkjhgfghjkjhgh';
$REVERB_HOST       = 'localhost';
$REVERB_PORT       = 8080;
$REVERB_SCHEME     = 'http'; // http ou https

$endpoint = "{$REVERB_SCHEME}://{$REVERB_HOST}:{$REVERB_PORT}/apps/{$REVERB_APP_ID}/events";

// Payload do evento
$payload = json_encode([
    'name' => 'reverb.test',
    'channels' => ['private-test'],
    'data' => json_encode([
        'message' => 'Conexão com Reverb OK',
        'timestamp' => date('Y-m-d H:i:s'),
    ]),
]);

$timestamp = time();
$bodyMd5 = md5($payload);

// String de assinatura (padrão Reverb / Pusher)
$stringToSign = "POST\n/apps/{$REVERB_APP_ID}/events\n"
    . "auth_key={$REVERB_APP_KEY}"
    . "&auth_timestamp={$timestamp}"
    . "&auth_version=1.0"
    . "&body_md5={$bodyMd5}";

$signature = hash_hmac('sha256', $stringToSign, $REVERB_APP_SECRET);

// Query autenticada
$query = http_build_query([
    'auth_key' => $REVERB_APP_KEY,
    'auth_timestamp' => $timestamp,
    'auth_version' => '1.0',
    'body_md5' => $bodyMd5,
    'auth_signature' => $signature,
]);

$url = "{$endpoint}?{$query}";

// CURL
$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// OUTPUT
echo "==============================\n";
echo " TESTE DE CONEXÃO - REVERB\n";
echo "==============================\n";
echo "URL: {$url}\n";
echo "HTTP STATUS: {$status}\n\n";

if ($error) {
    echo "❌ ERRO CURL:\n{$error}\n";
    exit(1);
}

if ($status >= 200 && $status < 300) {
    echo "✅ REVERB RESPONDEU COM SUCESSO\n";
} else {
    echo "⚠️ RESPOSTA INESPERADA\n";
}

echo "\nResposta:\n";
echo $response . "\n";
