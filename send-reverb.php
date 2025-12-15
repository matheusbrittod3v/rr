<?php

/**
 * SCRIPT PARA ENVIAR MENSAGEM VIA WEBSOCKET PARA LARAVEL REVERB
 * Uso: php send-reverb.php [mensagem]
 */

require_once __DIR__ . '/test-reverb.php';

$REVERB_APP_ID     = '345678';
$REVERB_APP_KEY    = 'sdfghjkhgfdfghjkhgfghjk';
$REVERB_APP_SECRET = 'dfghjkrtyuijhgfghjklkjhgfghjkjhgh';
$REVERB_HOST       = 'localhost';
$REVERB_PORT       = 8080;
$REVERB_SCHEME     = 'http'; // http ou https

// Mensagem a ser enviada (ou argumento da linha de comando)
$message = $argv[1] ?? 'Mensagem de teste via WebSocket';

// Canal para enviar
$channel = $argv[2] ?? 'private-test';

// Converter http para ws
$wsScheme = $REVERB_SCHEME === 'https' ? 'wss' : 'ws';
$wsUrl = "{$wsScheme}://{$REVERB_HOST}:{$REVERB_PORT}/app/{$REVERB_APP_KEY}";

echo "==============================\n";
echo " ENVIAR MENSAGEM - REVERB WS\n";
echo "==============================\n";
echo "Conectando em: {$wsUrl}\n";
echo "Canal: {$channel}\n";
echo "Mensagem: {$message}\n\n";

// Criar conexão WebSocket
$socket = @stream_socket_client(
    "tcp://{$REVERB_HOST}:{$REVERB_PORT}",
    $errno,
    $errstr,
    10,
    STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT
);

if (!$socket) {
    die("❌ Erro ao conectar: {$errstr} ({$errno})\n");
}

// Gerar chave para handshake WebSocket
$key = base64_encode(random_bytes(16));

// Handshake WebSocket
$handshake = "GET /app/{$REVERB_APP_KEY} HTTP/1.1\r\n"
    . "Host: {$REVERB_HOST}:{$REVERB_PORT}\r\n"
    . "Upgrade: websocket\r\n"
    . "Connection: Upgrade\r\n"
    . "Sec-WebSocket-Key: {$key}\r\n"
    . "Sec-WebSocket-Version: 13\r\n"
    . "Origin: http://{$REVERB_HOST}\r\n"
    . "\r\n";

fwrite($socket, $handshake);

// Ler resposta do handshake
$response = '';
while (!feof($socket)) {
    $line = fgets($socket, 1024);
    $response .= $line;
    if (strlen($line) < 3) break; // Headers terminam com \r\n\r\n
}

if (strpos($response, '101 Switching Protocols') === false) {
    fclose($socket);
    die("❌ Handshake falhou:\n{$response}\n");
}

echo "✅ Conectado com sucesso!\n\n";

// Primeiro, se inscrever no canal
$subscribeMessage = [
    'event' => 'pusher:subscribe',
    'data' => [
        'channel' => $channel,
    ],
];

// Se for canal privado, precisa de autenticação
if (strpos($channel, 'private-') === 0) {
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$REVERB_APP_KEY}:{$timestamp}:{$channel}", $REVERB_APP_SECRET);
    
    $subscribeMessage['data']['auth'] = "{$REVERB_APP_KEY}:{$signature}";
    $subscribeMessage['data']['channel_data'] = json_encode(['user_id' => 'test-user']);
}

$subscribePayload = json_encode($subscribeMessage);
$subscribeFrame = createWebSocketFrame($subscribePayload);
fwrite($socket, $subscribeFrame);

echo "📡 Inscrito no canal: {$channel}\n";
sleep(1); // Aguardar confirmação de inscrição

// Preparar mensagem no formato Pusher/Reverb (client events)
$eventData = [
    'event' => 'client-message',
    'channel' => $channel,
    'data' => json_encode([
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s'),
    ]),
];

$payload = json_encode($eventData);

// Enviar mensagem WebSocket (frame simples)
$frame = createWebSocketFrame($payload);
fwrite($socket, $frame);

echo "📤 Mensagem enviada!\n";
echo "Payload: {$payload}\n\n";

// Aguardar resposta (opcional)
stream_set_timeout($socket, 2);
$response = fread($socket, 1024);
if ($response) {
    $decoded = decodeWebSocketFrame($response);
    if ($decoded) {
        echo "📥 Resposta recebida:\n{$decoded}\n";
    }
}

fclose($socket);
echo "\n✅ Conexão fechada.\n";

/**
 * Cria um frame WebSocket simples
 */
function createWebSocketFrame($data) {
    $len = strlen($data);
    $frame = chr(0x81); // FIN + opcode text
    
    // Clientes devem mascarar frames
    $mask = random_bytes(4);
    $maskedLen = $len | 0x80;
    
    if ($len < 126) {
        $frame .= chr($maskedLen);
    } elseif ($len < 65536) {
        $frame .= chr(126 | 0x80) . pack('n', $len);
    } else {
        $frame .= chr(127 | 0x80) . pack('N', 0) . pack('N', $len);
    }
    
    $frame .= $mask;
    
    // Aplicar máscara aos dados
    for ($i = 0; $i < $len; $i++) {
        $frame .= $data[$i] ^ $mask[$i % 4];
    }
    
    return $frame;
}

/**
 * Decodifica um frame WebSocket simples
 */
function decodeWebSocketFrame($frame) {
    if (strlen($frame) < 2) return null;
    
    $firstByte = ord($frame[0]);
    $secondByte = ord($frame[1]);
    
    $masked = ($secondByte & 0x80) !== 0;
    $payloadLen = $secondByte & 0x7F;
    
    $offset = 2;
    
    if ($payloadLen === 126) {
        if (strlen($frame) < 4) return null;
        $payloadLen = unpack('n', substr($frame, 2, 2))[1];
        $offset = 4;
    } elseif ($payloadLen === 127) {
        if (strlen($frame) < 10) return null;
        $payloadLen = unpack('N', substr($frame, 6, 4))[1];
        $offset = 10;
    }
    
    if ($masked) {
        $mask = substr($frame, $offset, 4);
        $offset += 4;
        $payload = substr($frame, $offset);
        $decoded = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $decoded .= $payload[$i] ^ $mask[$i % 4];
        }
        return $decoded;
    } else {
        return substr($frame, $offset);
    }
}

