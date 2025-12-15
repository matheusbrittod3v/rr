# 🔒 Configuração Reverb para Produção com SSL/HTTPS

Este documento explica como configurar o Reverb para funcionar em produção com SSL/HTTPS forçado.

## ⚠️ Problema Comum

Quando a aplicação força HTTPS, o WebSocket precisa usar **WSS** (WebSocket Secure) na porta **443** (padrão HTTPS), não na porta da aplicação Laravel.

## 📋 Configuração .env para Produção

```env
# ============================================
# REVERB - PRODUÇÃO COM SSL
# ============================================

# Credenciais
REVERB_APP_ID=345678
REVERB_APP_KEY=sdfghjkhgfdfghjkhgfghjk
REVERB_APP_SECRET=dfghjkrtyuijhgfghjklkjhgfghjkjhgh

# Host do domínio (sem porta)
REVERB_HOST=renttool.store
# ou
# REVERB_HOST=uniontechnicals.com

# Porta para cliente (443 = HTTPS padrão)
REVERB_PORT=443

# Scheme HTTPS
REVERB_SCHEME=https

# Servidor Reverb (porta interna - não exposta)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# ============================================
# VITE (Frontend) - MESMAS CONFIGURAÇÕES
# ============================================
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 🔍 Como Funciona

### Em Produção (HTTPS):
- **REVERB_PORT=443**: Porta padrão HTTPS (não precisa especificar no código)
- **REVERB_SCHEME=https**: Força uso de WSS (WebSocket Secure)
- **REVERB_HOST**: Domínio sem porta (ex: `renttool.store`)

O código JavaScript detecta automaticamente:
- Se `scheme === 'https'` e `port === 443` → **não especifica porta** (usa padrão 443)
- Conecta via: `wss://renttool.store/app/{APP_KEY}`

### Em Desenvolvimento (HTTP):
- **REVERB_PORT=8080**: Porta customizada
- **REVERB_SCHEME=http**: Usa WS (WebSocket normal)
- **REVERB_HOST**: `localhost` ou IP

O código JavaScript:
- Especifica a porta: `ws://localhost:8080/app/{APP_KEY}`

## 🚀 Traefik (Proxy Reverso)

Se você usa Traefik, configure assim:

```yaml
http:
  routers:
    # Reverb WebSocket
    reverb-router-https:
      rule: Host(`renttool.store`) && PathPrefix(`/app/`)
      service: reverb-service
      entryPoints:
        - websecure
      tls:
        certResolver: letsencrypt

  services:
    reverb-service:
      loadBalancer:
        servers:
          - url: http://aplication-pdr-laravel-aplication-dqvnri:8080
```

**Importante**: 
- Traefik recebe em `443` (HTTPS)
- Traefik roteia para Reverb na porta `8080` (interna)
- Cliente conecta em `wss://renttool.store/app/{APP_KEY}` (porta 443 padrão)

## ✅ Verificação

### 1. Verificar Variáveis de Ambiente
```bash
php artisan config:show reverb
php artisan config:show broadcasting
```

### 2. Testar Conexão WebSocket
No console do navegador (F12), você deve ver:
```
[Echo] ✅ Laravel Echo inicializado com sucesso!
[Echo] Conectando em: https://renttool.store
```

### 3. Verificar Conexão WebSocket
No console do navegador, verifique se a conexão WebSocket está ativa:
```javascript
// No console do navegador
window.Echo.connector.socket.readyState
// Deve retornar: 1 (OPEN)
```

### 4. Testar com Script PHP
Atualize `test-reverb.php`:
```php
$REVERB_HOST = 'renttool.store';
$REVERB_PORT = 443;
$REVERB_SCHEME = 'https';
```

Execute:
```bash
php test-reverb.php
```

## 🐛 Troubleshooting

### Erro: "WebSocket is closed before the connection is established"
**Causa**: Porta errada ou scheme incorreto
**Solução**: 
- Verifique se `REVERB_PORT=443` e `REVERB_SCHEME=https`
- Verifique se `VITE_REVERB_*` está no `.env`
- Recompile: `npm run build`

### Erro: "WebSocket opening handshake timed out"
**Causa**: Reverb não está rodando ou Traefik não está roteando corretamente
**Solução**:
- Verifique se Reverb está rodando: `php artisan reverb:start`
- Verifique se Traefik está roteando `/app/*` para Reverb
- Verifique logs do Traefik

### Erro: "Mixed Content" (HTTP em página HTTPS)
**Causa**: Tentando usar `http://` em página `https://`
**Solução**: 
- Use `REVERB_SCHEME=https`
- Use `REVERB_PORT=443`
- Recompile assets

### WebSocket conecta mas não recebe mensagens
**Causa**: Canal não está inscrito ou evento não está sendo broadcastado
**Solução**:
- Verifique se o canal está correto: `chat-room.{roomCode}`
- Verifique se o evento está implementando `ShouldBroadcast`
- Verifique se `BROADCAST_CONNECTION=reverb` no `.env`

## 📝 Checklist de Produção

- [ ] `.env` configurado com `REVERB_SCHEME=https` e `REVERB_PORT=443`
- [ ] Variáveis `VITE_REVERB_*` no `.env`
- [ ] Assets compilados: `npm run build`
- [ ] Reverb rodando: `php artisan reverb:start`
- [ ] Traefik configurado para rotear `/app/*` para Reverb
- [ ] Certificado SSL válido no Traefik
- [ ] Teste de conexão WebSocket funcionando

