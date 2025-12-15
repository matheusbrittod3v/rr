<?php

/**
 * SCRIPT PARA ESCUTAR MENSAGENS VIA WEBSOCKET DO LARAVEL REVERB
 * Uso: php listen-reverb.php [canal]
 * Pressione Ctrl+C para sair
 */

require_once __DIR__ . '/test-reverb.php';

$REVERB_APP_ID     = '345678';
$REVERB_APP_KEY    = 'sdfghjkhgfdfghjkhgfghjk';
$REVERB_APP_SECRET = 'dfghjkrtyuijhgfghjklkjhgfghjkjhgh';
$REVERB_HOST       = 'localhost';
$REVERB_PORT       = 8080;
$REVERB_SCHEME     = 'http'; // http ou https

// Canal para escutar (ou argumento da linha de comando)
$channel = $argv[1] ?? 'private-test';

// Converter http para ws
$wsScheme = $REVERB_SCHEME === 'https' ? 'wss' : 'ws';
$wsUrl = "{$wsScheme}://{$REVERB_HOST}:{$REVERB_PORT}/app/{$REVERB_APP_KEY}";

echo "==============================\n";
echo " ESCUTAR MENSAGENS - REVERB WS\n";
echo "==============================\n";
echo "Conectando em: {$wsUrl}\n";
echo "Canal: {$channel}\n";
echo "Pressione Ctrl+C para sair\n\n";

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
    if (strlen($line) < 3) break;
}

if (strpos($response, '101 Switching Protocols') === false) {
    fclose($socket);
    die("❌ Handshake falhou:\n{$response}\n");
}

echo "✅ Conectado com sucesso!\n\n";

// Assinar o canal (formato Pusher/Reverb)
$subscribeMessage = [
    'event' => 'pusher:subscribe',
    'data' => [
        'channel' => $channel,
    ],
];

// Se for canal privado, precisa de autenticação
if (strpos($channel, 'private-') === 0) {
    $timestamp = time();
    $authString = "{$REVERB_APP_KEY}:{$REVERB_APP_SECRET}";
    $signature = hash_hmac('sha256', "{$REVERB_APP_KEY}:{$timestamp}:{$channel}", $REVERB_APP_SECRET);
    
    $subscribeMessage['data']['auth'] = "{$REVERB_APP_KEY}:{$signature}";
    $subscribeMessage['data']['channel_data'] = json_encode(['user_id' => 'test-user']);
}

$subscribePayload = json_encode($subscribeMessage);
$subscribeFrame = createWebSocketFrame($subscribePayload);
fwrite($socket, $subscribeFrame);

echo "📡 Inscrito no canal: {$channel}\n";
echo "👂 Aguardando mensagens...\n\n";

// Configurar socket para não bloquear
stream_set_blocking($socket, false);

// Loop principal para receber mensagens
$buffer = '';
$lastPing = time();

while (true) {
    // Ler dados do socket
    $data = fread($socket, 8192);
    
    if ($data !== false && strlen($data) > 0) {
        $buffer .= $data;
        
        // Tentar decodificar frames completos
        while (strlen($buffer) >= 2) {
            $decoded = decodeWebSocketFrame($buffer);
            
            if ($decoded !== null) {
                // Remover frame processado do buffer
                $frameLen = getWebSocketFrameLength($buffer);
                $buffer = substr($buffer, $frameLen);
                
                // Processar mensagem
                $message = json_decode($decoded, true);
                
                if ($message) {
                    echo "[" . date('H:i:s') . "] 📨 Nova mensagem:\n";
                    echo json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
                } else {
                    echo "[" . date('H:i:s') . "] 📨 Dados recebidos:\n";
                    echo $decoded . "\n\n";
                }
            } else {
                // Frame incompleto, aguardar mais dados
                break;
            }
        }
    }
    
    // Enviar ping a cada 30 segundos (keep-alive)
    if (time() - $lastPing > 30) {
        $pingFrame = createWebSocketFrame(json_encode(['event' => 'pusher:ping', 'data' => []]));
        fwrite($socket, $pingFrame);
        $lastPing = time();
    }
    
    // Pequena pausa para não sobrecarregar CPU
    usleep(100000); // 0.1 segundo
    
    // Verificar se conexão ainda está ativa
    if (feof($socket)) {
        echo "\n❌ Conexão fechada pelo servidor.\n";
        break;
    }
}

fclose($socket);
echo "\n✅ Script finalizado.\n";

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
        if (strlen($frame) < $offset + 4 + $payloadLen) return null;
        $mask = substr($frame, $offset, 4);
        $offset += 4;
        $payload = substr($frame, $offset, $payloadLen);
        $decoded = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $decoded .= $payload[$i] ^ $mask[$i % 4];
        }
        return $decoded;
    } else {
        if (strlen($frame) < $offset + $payloadLen) return null;
        return substr($frame, $offset, $payloadLen);
    }
}

/**
 * Calcula o tamanho de um frame WebSocket
 */
function getWebSocketFrameLength($frame) {
    if (strlen($frame) < 2) return 0;
    
    $secondByte = ord($frame[1]);
    $payloadLen = $secondByte & 0x7F;
    $masked = ($secondByte & 0x80) !== 0;
    
    $offset = 2;
    
    if ($payloadLen === 126) {
        if (strlen($frame) < 4) return 0;
        $payloadLen = unpack('n', substr($frame, 2, 2))[1];
        $offset = 4;
    } elseif ($payloadLen === 127) {
        if (strlen($frame) < 10) return 0;
        $payloadLen = unpack('N', substr($frame, 6, 4))[1];
        $offset = 10;
    }
    
    if ($masked) {
        return $offset + 4 + $payloadLen;
    } else {
        return $offset + $payloadLen;
    }
}

