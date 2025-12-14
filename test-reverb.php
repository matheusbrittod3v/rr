<?php

/**
 * Script simples para testar conexão com servidor Reverb
 * 
 * Uso: php test-reverb.php
 */

// Carregar variáveis do arquivo .env se existir
function loadEnvFile($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentários
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove aspas se existirem
            $value = trim($value, '"\'');
            
            // Define a variável de ambiente se não estiver já definida
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Carregar .env do diretório atual
loadEnvFile(__DIR__ . '/.env');

// Configurações - Carrega do .env ou usa valores padrão
$config = [
    'host' => getenv('REVERB_HOST') ?: 'localhost',
    'port' => (int)(getenv('REVERB_PORT') ?: 8080),
    'app_key' => getenv('REVERB_APP_KEY') ?: 'reverb-app-key',
    'app_id' => getenv('REVERB_APP_ID') ?: '',
    'app_secret' => getenv('REVERB_APP_SECRET') ?: '',
    'scheme' => getenv('REVERB_SCHEME') ?: 'http',
];

// Função para imprimir resultados
function printResult($test, $success, $message = '') {
    $icon = $success ? '✅' : '❌';
    echo sprintf("%s %s", $icon, $test);
    if ($message) {
        echo " - $message";
    }
    echo "\n";
}

echo "🔍 Testando conexão com servidor Reverb\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Host: {$config['host']}\n";
echo "Porta: {$config['port']}\n";
echo "App Key: {$config['app_key']}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Teste 1: Verificar se a porta está aberta
echo "1. Testando conexão TCP na porta {$config['port']}...\n";
$socket = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
if ($socket) {
    printResult("Conexão TCP", true, "Porta {$config['port']} está acessível");
    fclose($socket);
} else {
    printResult("Conexão TCP", false, "Erro $errno: $errstr");
    echo "\n❌ Não foi possível conectar. Verifique se o servidor Reverb está rodando.\n";
    exit(1);
}
echo "\n";

// Teste 2: Testar conexão HTTP (reverb responde a requisições HTTP também)
echo "2. Testando resposta HTTP...\n";
$url = "{$config['scheme']}://{$config['host']}:{$config['port']}/app/{$config['app_key']}";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    printResult("Resposta HTTP", false, "Erro: $error");
} else {
    // Reverb pode retornar 400 (Bad Request) para requisições HTTP normais
    // mas isso significa que o servidor está respondendo
    if ($httpCode > 0) {
        printResult("Resposta HTTP", true, "HTTP Status: $httpCode (servidor está respondendo)");
    } else {
        printResult("Resposta HTTP", false, "Nenhuma resposta recebida");
    }
}
echo "\n";

// Teste 3: Tentar upgrade para WebSocket (simulação básica)
echo "3. Testando handshake WebSocket...\n";
$wsKey = base64_encode(random_bytes(16));
$wsUrl = "ws://{$config['host']}:{$config['port']}/app/{$config['app_key']}";

// Criar handshake WebSocket manualmente
$socket = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
if ($socket) {
    $handshake = "GET /app/{$config['app_key']} HTTP/1.1\r\n";
    $handshake .= "Host: {$config['host']}:{$config['port']}\r\n";
    $handshake .= "Upgrade: websocket\r\n";
    $handshake .= "Connection: Upgrade\r\n";
    $handshake .= "Sec-WebSocket-Key: $wsKey\r\n";
    $handshake .= "Sec-WebSocket-Version: 13\r\n";
    $handshake .= "\r\n";
    
    fwrite($socket, $handshake);
    $response = fread($socket, 1024);
    fclose($socket);
    
    if (strpos($response, '101 Switching Protocols') !== false) {
        printResult("Handshake WebSocket", true, "Upgrade bem-sucedido!");
    } elseif (strpos($response, 'HTTP/') !== false) {
        // Servidor respondeu, mas não aceitou o upgrade
        // Isso pode ser normal dependendo da configuração
        printResult("Handshake WebSocket", true, "Servidor respondeu (verifique autenticação)");
        echo "   Resposta: " . substr($response, 0, 200) . "\n";
    } else {
        printResult("Handshake WebSocket", false, "Nenhuma resposta válida");
    }
} else {
    printResult("Handshake WebSocket", false, "Não foi possível conectar");
}
echo "\n";

// Teste 4: Verificar variáveis de ambiente
echo "4. Verificando variáveis de ambiente...\n";
$requiredVars = ['REVERB_APP_ID', 'REVERB_APP_KEY', 'REVERB_APP_SECRET'];
$allSet = true;
foreach ($requiredVars as $var) {
    $value = getenv($var) ?: ($config[strtolower(str_replace('REVERB_', '', $var))] ?? '');
    if ($value) {
        // Mostra apenas os primeiros e últimos caracteres por segurança
        $displayValue = strlen($value) > 10 
            ? substr($value, 0, 4) . '...' . substr($value, -4)
            : str_repeat('*', strlen($value));
        printResult($var, true, "Definida ($displayValue)");
    } else {
        printResult($var, false, "Não definida");
        $allSet = false;
    }
}
echo "\n";

// Resumo
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
if ($socket || $httpCode > 0) {
    echo "✅ Servidor Reverb parece estar funcionando!\n";
    echo "\n";
    echo "📝 Próximos passos:\n";
    echo "   1. Verifique os logs do servidor para mais detalhes\n";
    echo "   2. Teste uma conexão WebSocket completa com Laravel Echo\n";
    echo "   3. Verifique se as credenciais estão corretas\n";
} else {
    echo "❌ Servidor Reverb não está respondendo corretamente\n";
    echo "\n";
    echo "🔧 Soluções:\n";
    echo "   1. Verifique se o servidor está rodando: php artisan reverb:start\n";
    echo "   2. Verifique se a porta {$config['port']} está correta\n";
    echo "   3. Verifique firewall/proxy\n";
    echo "   4. Verifique os logs do servidor\n";
}
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

