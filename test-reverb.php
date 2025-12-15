<?php

/**
 * TESTE DE CONEXÃO COM LARAVEL REVERB (PHP PURO)
 * 
 * Configuração hardcoded para produção (VPS)
 * 
 * Uso:
 *   php test-reverb.php                    # Testa produção (padrão)
 *   php test-reverb.php localhost           # Testa localhost
 */

// ============================================
// CONFIGURAÇÕES HARDCODED - PRODUÇÃO (VPS)
// ============================================
$REVERB_APP_ID     = '345678';
$REVERB_APP_KEY    = 'sdfghjkhgfdfghjkhgfghjk';
$REVERB_APP_SECRET = 'dfghjkrtyuijhgfghjklkjhgfghjkjhgh';
$REVERB_HOST       = 'renttool.store';  // ou 'uniontechnicals.com'
$REVERB_PORT       = '443';
$REVERB_SCHEME     = 'https';

// Detectar se é localhost via argumento
$mode = $argv[1] ?? 'production';

if ($mode === 'localhost') {
    // Configuração de localhost (para testes locais)
    $REVERB_HOST   = 'localhost';
    $REVERB_SCHEME = 'http';
    $REVERB_PORT   = '8080';
}

// Construir endpoint
// Em produção HTTPS (porta 443), não especificamos porta na URL
if ($REVERB_SCHEME === 'https' && $REVERB_PORT === '443') {
    $endpoint = "{$REVERB_SCHEME}://{$REVERB_HOST}/apps/{$REVERB_APP_ID}/events";
} else {
    $endpoint = "{$REVERB_SCHEME}://{$REVERB_HOST}:{$REVERB_PORT}/apps/{$REVERB_APP_ID}/events";
}

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
    CURLOPT_SSL_VERIFYPEER => $REVERB_SCHEME === 'https', // Verificar SSL em produção
    CURLOPT_SSL_VERIFYHOST => $REVERB_SCHEME === 'https' ? 2 : false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// OUTPUT
echo "==============================\n";
echo " TESTE DE CONEXÃO - REVERB\n";
echo "==============================\n";
echo "Ambiente: " . ($mode === 'localhost' ? 'LOCALHOST' : 'PRODUÇÃO (VPS)') . "\n";
echo "Host: {$REVERB_HOST}\n";
echo "Scheme: {$REVERB_SCHEME}\n";
echo "Porta: {$REVERB_PORT}\n";
echo "URL: {$url}\n";
echo "HTTP STATUS: {$status}\n\n";

if ($error) {
    echo "❌ ERRO CURL:\n{$error}\n\n";
    echo "💡 DICAS:\n";
    echo "   - Verifique se o Reverb está rodando\n";
    echo "   - Verifique se a porta está correta\n";
    echo "   - Verifique se o host está acessível\n";
    if ($REVERB_SCHEME === 'https') {
        echo "   - Verifique se o certificado SSL é válido\n";
    }
    exit(1);
}

if ($status >= 200 && $status < 300) {
    echo "✅ REVERB RESPONDEU COM SUCESSO\n";
    echo "✅ Conexão estabelecida corretamente!\n";
} else {
    echo "⚠️ RESPOSTA INESPERADA\n";
    echo "Status HTTP: {$status}\n";
    
    if ($status === 0) {
        echo "\n💡 Possíveis causas:\n";
        echo "   - Servidor não está acessível\n";
        echo "   - Porta incorreta ou bloqueada\n";
        echo "   - Firewall bloqueando conexão\n";
    } elseif ($status === 404) {
        echo "\n💡 Possíveis causas:\n";
        echo "   - Endpoint incorreto\n";
        echo "   - REVERB_APP_ID incorreto\n";
    } elseif ($status === 401 || $status === 403) {
        echo "\n💡 Possíveis causas:\n";
        echo "   - Credenciais incorretas (APP_KEY, APP_SECRET)\n";
        echo "   - Assinatura inválida\n";
    }
}

echo "\nResposta do servidor:\n";
if (empty($response)) {
    echo "(vazio)\n";
} else {
    echo $response . "\n";
}

echo "\n";
