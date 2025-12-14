<?php

/**
 * Versão ainda mais simples - apenas testa se a porta está aberta
 * 
 * Uso: php test-reverb-simple.php
 */

// Carregar variáveis do arquivo .env se existir
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

$host = getenv('REVERB_HOST') ?: 'localhost';
$port = (int)(getenv('REVERB_PORT') ?: 8080);

echo "🔍 Testando Reverb em {$host}:{$port}...\n\n";

$socket = @fsockopen($host, $port, $errno, $errstr, 3);

if ($socket) {
    echo "✅ SUCESSO! Servidor Reverb está acessível na porta {$port}\n";
    fclose($socket);
    exit(0);
} else {
    echo "❌ ERRO! Não foi possível conectar\n";
    echo "   Código: $errno\n";
    echo "   Mensagem: $errstr\n\n";
    echo "💡 Verifique:\n";
    echo "   - Se o servidor está rodando: php artisan reverb:start\n";
    echo "   - Se a porta {$port} está correta\n";
    echo "   - Se o firewall permite conexões\n";
    exit(1);
}

