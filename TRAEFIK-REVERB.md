# 🔧 Configuração Traefik para Laravel + Reverb

Este documento explica como configurar o Traefik para rotear tráfego HTTP/HTTPS para a aplicação Laravel e WebSocket para o Reverb.

## 📋 Estrutura

- **Domínio**: `uniontechnicals.com`
- **Aplicação Laravel**: Porta `8082`
- **Reverb WebSocket**: Porta `8080`
- **Porta HTTP (Traefik)**: `80` → Redireciona para HTTPS
- **Porta HTTPS (Traefik)**: `443` → Serve aplicação e WebSocket

## 🚀 Como Funciona

### Roteamento

1. **Requisições HTTP normais** (`uniontechnicals.com/*`):
   - Traefik porta 80 → Redireciona para HTTPS
   - Traefik porta 443 → Roteia para Laravel (porta 8082)

2. **Requisições WebSocket** (`uniontechnicals.com/app/*`):
   - Traefik porta 80 → Redireciona para HTTPS
   - Traefik porta 443 → Roteia para Reverb (porta 8080)

### Regras de Roteamento

- **Laravel**: `Host(uniontechnicals.com) && !PathPrefix(/app/)`
  - Todas as requisições EXCETO `/app/*` vão para Laravel

- **Reverb**: `Host(uniontechnicals.com) && PathPrefix(/app/)`
  - Todas as requisições `/app/*` vão para Reverb

## 📝 Configuração Traefik

Use o arquivo `traefik-config.yml` que contém:

```yaml
http:
  routers:
    # Laravel HTTP → HTTPS
    aplication-pdr-laravel-aplication-dqvnri-router-12:
      rule: Host(`uniontechnicals.com`) && !PathPrefix(`/app/`)
      service: aplication-pdr-laravel-aplication-dqvnri-service-12
      middlewares:
        - redirect-to-https
      entryPoints:
        - web
    
    # Laravel HTTPS
    aplication-pdr-laravel-aplication-dqvnri-router-websecure-12:
      rule: Host(`uniontechnicals.com`) && !PathPrefix(`/app/`)
      service: aplication-pdr-laravel-aplication-dqvnri-service-12
      entryPoints:
        - websecure
      tls:
        certResolver: letsencrypt
    
    # Reverb HTTP → HTTPS
    reverb-router-http:
      rule: Host(`uniontechnicals.com`) && PathPrefix(`/app/`)
      service: reverb-service
      middlewares:
        - redirect-to-https
      entryPoints:
        - web
    
    # Reverb HTTPS/WSS
    reverb-router-https:
      rule: Host(`uniontechnicals.com`) && PathPrefix(`/app/`)
      service: reverb-service
      entryPoints:
        - websecure
      tls:
        certResolver: letsencrypt

  services:
    # Laravel na porta 8082
    aplication-pdr-laravel-aplication-dqvnri-service-12:
      loadBalancer:
        servers:
          - url: http://aplication-pdr-laravel-aplication-dqvnri:8082
    
    # Reverb na porta 8080
    reverb-service:
      loadBalancer:
        servers:
          - url: http://aplication-pdr-laravel-aplication-dqvnri:8080

  middlewares:
    redirect-to-https:
      redirectScheme:
        scheme: https
        permanent: true
```

## ⚙️ Variáveis de Ambiente (.env)

Para produção, configure no `.env`:

```env
# Reverb - Produção
REVERB_APP_ID=345678
REVERB_APP_KEY=sdfghjkhgfdfghjkhgfghjk
REVERB_APP_SECRET=dfghjkrtyuijhgfghjklkjhgfghjkjhgh
REVERB_HOST=uniontechnicals.com
REVERB_PORT=443
REVERB_SCHEME=https

# Reverb - Servidor (interno)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Vite (Frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 🔍 Verificação

### 1. Testar Aplicação Laravel
```bash
curl -I https://uniontechnicals.com
# Deve retornar 200 OK
```

### 2. Testar Reverb WebSocket
```bash
curl -I https://uniontechnicals.com/app/sdfghjkhgfdfghjkhgfghjk
# Deve retornar 101 Switching Protocols (WebSocket upgrade)
```

### 3. Testar com Script PHP
Atualize `test-reverb.php` para usar:
```php
$REVERB_HOST = 'uniontechnicals.com';
$REVERB_PORT = 443;
$REVERB_SCHEME = 'https';
```

## ⚠️ Notas Importantes

1. **PathPrefix `/app/`**: O Reverb usa o path `/app/{APP_KEY}` para conexões WebSocket
2. **WebSocket Upgrade**: O Traefik detecta automaticamente o header `Upgrade: websocket`
3. **TLS**: Certificados SSL são gerenciados pelo Let's Encrypt via `certResolver: letsencrypt`
4. **Prioridade**: Os routers do Reverb têm prioridade maior (PathPrefix `/app/`)

## 🐛 Troubleshooting

### WebSocket não conecta
- Verifique se o Reverb está rodando na porta 8080
- Verifique se o Traefik está roteando `/app/*` corretamente
- Verifique os logs do Traefik: `docker logs traefik`

### Aplicação Laravel não carrega
- Verifique se o Laravel está rodando na porta 8082
- Verifique se o Traefik está roteando corretamente (sem `/app/`)
- Verifique os logs: `docker logs aplication-pdr-laravel-aplication-dqvnri`

### Erro de certificado SSL
- Verifique se o Let's Encrypt está configurado corretamente
- Verifique se o domínio está apontando para o servidor correto
- Verifique os logs do Traefik para erros de certificado

