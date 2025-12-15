# 🔧 Configuração Reverb para Localhost

Este documento contém as configurações necessárias para rodar o Laravel Reverb em localhost.

## 📋 Variáveis de Ambiente (.env)

Adicione ou atualize as seguintes variáveis no seu arquivo `.env`:

```env
# Broadcasting - Ativa Reverb
BROADCAST_CONNECTION=reverb

# Reverb - Credenciais (localhost)
REVERB_APP_ID=345678
REVERB_APP_KEY=sdfghjkhgfdfghjkhgfghjk
REVERB_APP_SECRET=dfghjkrtyuijhgfghjklkjhgfghjkjhgh

# Reverb - Host (localhost)
REVERB_HOST=localhost

# Reverb - Servidor (onde o servidor Reverb escuta)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Reverb - Cliente (porta e scheme para conexão)
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite (Frontend) - Mesmas configurações para o frontend
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Reverb - Configurações opcionais
REVERB_MAX_REQUEST_SIZE=10000
REVERB_APP_PING_INTERVAL=60
REVERB_APP_ACTIVITY_TIMEOUT=30
REVERB_APP_MAX_CONNECTIONS=
REVERB_APP_MAX_MESSAGE_SIZE=10000

# Reverb - Scaling (desabilitado para localhost)
REVERB_SCALING_ENABLED=false
REVERB_SCALING_CHANNEL=reverb

# Reverb - Integração com Pulse/Telescope
REVERB_PULSE_INGEST_INTERVAL=15
REVERB_TELESCOPE_INGEST_INTERVAL=15
```

## 🚀 Como Iniciar

### 1. Configurar o .env
Copie as variáveis acima para o seu arquivo `.env`

### 2. Limpar cache de configuração
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Compilar assets do frontend
```bash
npm run build
# ou para desenvolvimento
npm run dev
```

### 4. Iniciar o servidor Reverb
Em um terminal, execute:
```bash
php artisan reverb:start
```

O servidor Reverb estará rodando em `http://localhost:8080`

### 5. Iniciar o servidor Laravel
Em outro terminal, execute:
```bash
php artisan serve
```

O servidor Laravel estará rodando em `http://localhost:8000`

### 6. (Opcional) Iniciar worker de filas
Se você usa filas para processar mensagens do chat:
```bash
php artisan queue:work
```

## ✅ Testar a Conexão

### Teste 1: Script PHP de teste
```bash
php test-reverb.php
```

### Teste 2: Enviar mensagem via WebSocket
```bash
php send-reverb.php "Minha mensagem de teste"
```

### Teste 3: Escutar mensagens
```bash
php listen-reverb.php "private-test"
```

## 📝 Arquivos Atualizados

Os seguintes arquivos foram atualizados para usar localhost:

- ✅ `config/broadcasting.php` - Configuração padrão para localhost
- ✅ `config/reverb.php` - Configuração do servidor Reverb para localhost
- ✅ `resources/js/websocket.js` - Frontend configurado para localhost
- ✅ `vite.config.js` - Vite configurado para localhost
- ✅ `test-reverb.php` - Script de teste para localhost
- ✅ `send-reverb.php` - Script de envio para localhost
- ✅ `listen-reverb.php` - Script de escuta para localhost

## 🔍 Verificar Configuração

Para verificar se tudo está configurado corretamente:

```bash
# Ver configuração de broadcasting
php artisan config:show broadcasting.default

# Ver configuração do Reverb
php artisan config:show reverb
```

## ⚠️ Notas Importantes

1. **Porta 8080**: O Reverb usa a porta 8080 por padrão em localhost
2. **HTTP**: Em localhost, use `http` (não `https`)
3. **Mesma porta**: Tanto `REVERB_SERVER_PORT` quanto `REVERB_PORT` devem ser 8080 em localhost
4. **CORS**: O Reverb está configurado para aceitar conexões de qualquer origem (`allowed_origins: ['*']`)

## 🐛 Troubleshooting

### Erro: "Connection refused"
- Verifique se o servidor Reverb está rodando: `php artisan reverb:start`
- Verifique se a porta 8080 está livre: `netstat -an | findstr 8080` (Windows) ou `lsof -i :8080` (Linux/Mac)

### Erro: "WebSocket connection failed"
- Verifique se as variáveis `VITE_REVERB_*` estão no `.env`
- Recompile os assets: `npm run build`
- Limpe o cache: `php artisan config:clear`

### Mensagens não aparecem no chat
- Verifique se o worker de filas está rodando: `php artisan queue:work`
- Verifique os logs: `storage/logs/laravel.log`
- Verifique o console do navegador para erros de WebSocket

