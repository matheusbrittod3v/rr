# 🔧 Variáveis de Ambiente - Broadcasting e Reverb

Este documento lista todas as variáveis de ambiente necessárias para configurar Broadcasting e Reverb no Laravel.

## 📋 Broadcasting (Básico)

### Ativar Broadcasting

```env
# Driver de broadcasting padrão
# Opções: null, log, reverb, pusher, soketi, ably
BROADCAST_CONNECTION=reverb
```

## 🚀 Laravel Reverb

### Variáveis Obrigatórias (para usar Reverb)

```env
# Credenciais da aplicação Reverb
REVERB_APP_ID=seu-app-id-unico
REVERB_APP_KEY=sua-chave-publica
REVERB_APP_SECRET=sua-chave-secreta

# Host do Reverb (domínio ou IP)
REVERB_HOST=reverb.seudominio.com
```

### Variáveis Opcionais (Reverb)

```env
# Configuração do servidor Reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_SERVER_PATH=

# Configuração de conexão (cliente)
REVERB_PORT=443
REVERB_SCHEME=https

# Limites e timeouts
REVERB_MAX_REQUEST_SIZE=10000
REVERB_APP_PING_INTERVAL=60
REVERB_APP_ACTIVITY_TIMEOUT=30
REVERB_APP_MAX_CONNECTIONS=
REVERB_APP_MAX_MESSAGE_SIZE=10000

# Scaling (para múltiplos servidores)
REVERB_SCALING_ENABLED=false
REVERB_SCALING_CHANNEL=reverb

# Integração com Pulse/Telescope
REVERB_PULSE_INGEST_INTERVAL=15
REVERB_TELESCOPE_INGEST_INTERVAL=15
```

### Variáveis Vite (Frontend)

```env
# Essas variáveis são usadas pelo Vite e precisam ter o prefixo VITE_
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 📡 Pusher (Alternativa ao Reverb)

```env
# Para usar Pusher, defina BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=seu-pusher-app-id
PUSHER_APP_KEY=seu-pusher-app-key
PUSHER_APP_SECRET=seu-pusher-app-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
```

## 🔌 Soketi (Alternativa ao Reverb/Pusher)

```env
# Para usar Soketi, defina BROADCAST_CONNECTION=soketi
SOKETI_APP_ID=
SOKETI_APP_KEY=
SOKETI_APP_SECRET=
SOKETI_APP_CLUSTER=mt1
SOKETI_HOST=
SOKETI_PORT=6001
SOKETI_SCHEME=http
SOKETI_ENCRYPTED=false
SOKETI_USE_TLS=false
```

## 🔑 Ably (Alternativa ao Reverb/Pusher)

```env
# Para usar Ably, defina BROADCAST_CONNECTION=ably
ABLY_KEY=
```

## 📝 Exemplo Completo (.env)

```env
# Broadcasting - Ativa Reverb
BROADCAST_CONNECTION=reverb

# Reverb - Credenciais
REVERB_APP_ID=app-1234567890
REVERB_APP_KEY=reverb-app-key-abc123
REVERB_APP_SECRET=reverb-app-secret-xyz789
REVERB_HOST=reverb.seudominio.com

# Reverb - Servidor
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Reverb - Cliente
REVERB_PORT=443
REVERB_SCHEME=https

# Vite (Frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## ✅ Verificação

Após configurar as variáveis, verifique:

1. **Cache de configuração:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. **Verificar configuração:**
   ```bash
   php artisan config:show broadcasting.default
   php artisan config:show reverb
   ```

3. **Testar Reverb:**
   ```bash
   php artisan reverb:start
   ```

## 🔍 Mapeamento de Variáveis

| Variável .env | Arquivo Config | Uso |
|--------------|----------------|-----|
| `BROADCAST_CONNECTION` | `config/broadcasting.php` | Driver padrão de broadcasting |
| `REVERB_APP_ID` | `config/reverb.php` | ID da aplicação Reverb |
| `REVERB_APP_KEY` | `config/reverb.php` | Chave pública Reverb |
| `REVERB_APP_SECRET` | `config/reverb.php` | Chave secreta Reverb |
| `REVERB_HOST` | `config/reverb.php` | Host do servidor Reverb |
| `REVERB_SERVER_HOST` | `config/reverb.php` | Host onde o servidor escuta |
| `REVERB_SERVER_PORT` | `config/reverb.php` | Porta onde o servidor escuta |
| `REVERB_PORT` | `config/reverb.php` | Porta do cliente (geralmente 443) |
| `REVERB_SCHEME` | `config/reverb.php` | Scheme (http/https) |
| `VITE_REVERB_APP_KEY` | `vite.config.js` | Chave para frontend |
| `VITE_REVERB_HOST` | `vite.config.js` | Host para frontend |
| `VITE_REVERB_PORT` | `vite.config.js` | Porta para frontend |
| `VITE_REVERB_SCHEME` | `vite.config.js` | Scheme para frontend |

## ⚠️ Notas Importantes

1. **Todas as variáveis Reverb** são usadas tanto em `config/reverb.php` quanto em `config/broadcasting.php`
2. **Variáveis Vite** (com prefixo `VITE_`) são expostas ao frontend e devem usar os mesmos valores das variáveis backend
3. **REVERB_SERVER_PORT** (8080) é a porta onde o servidor Reverb escuta
4. **REVERB_PORT** (443) é a porta que os clientes usam para conectar
5. Em produção, certifique-se de usar **HTTPS** (`REVERB_SCHEME=https`)

