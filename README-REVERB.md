# Servidor Laravel Reverb - Branch `reverb`

Esta branch está configurada para rodar **apenas como servidor Reverb**, sem a aplicação web completa. É ideal para criar um serviço dedicado de WebSockets separado da aplicação principal.

## 📋 Características

- ✅ Apenas servidor Reverb (sem nginx, sem PHP-FPM)
- ✅ Sem build de assets (npm/node não necessário)
- ✅ Configuração mínima otimizada para WebSockets
- ✅ Ideal para deployment separado em container/servidor dedicado

## 🚀 Como Usar

### Desenvolvimento Local

```bash
# Certifique-se de estar na branch reverb
git checkout reverb

# Configure as variáveis de ambiente no .env
cp .env.example .env
php artisan key:generate

# Configure o Reverb
REVERB_APP_ID=reverb-app-id
REVERB_APP_KEY=reverb-app-key
REVERB_APP_SECRET=reverb-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Inicie o servidor
php artisan reverb:start

# Você deve ver uma mensagem como:
# [server:info] Server starting on port 8080
# Reverb server started on 0.0.0.0:8080

# Teste se o servidor está rodando (em outro terminal):
php test-reverb.php
# Ou versão simples:
php test-reverb-simple.php
```

### Deployment no Coolify/Dockploy

1. **Crie uma nova aplicação** no Coolify/Dockploy apontando para esta branch `reverb`
2. **Configure a porta do container:** `8080` (mesma porta configurada em `REVERB_SERVER_PORT`)
3. **Configure as variáveis de ambiente:**

```env
# Configurações básicas do Laravel
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_ENV=production
APP_DEBUG=false

# Configurações do Reverb
REVERB_APP_ID=seu-app-id-unico
REVERB_APP_KEY=sua-chave-publica
REVERB_APP_SECRET=sua-chave-secreta
REVERB_HOST=seu-dominio-reverb.com
REVERB_PORT=8080
REVERB_SCHEME=https

# Configurações do servidor Reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_MAX_REQUEST_SIZE=10000

# Redis (opcional, para scaling)
REVERB_SCALING_ENABLED=false
REDIS_HOST=redis-host
REDIS_PASSWORD=senha-redis
REDIS_PORT=6379
```

3. **A aplicação iniciará automaticamente** o servidor Reverb na porta configurada

## 🔧 Arquitetura Recomendada

### Aplicação 1: Servidor Reverb (Esta Branch)
- **Branch**: `reverb`
- **Porta**: 8080 (ou a porta configurada)
- **Função**: Apenas servidor WebSocket
- **Comando**: `php artisan reverb:start`

### Aplicação 2: Aplicação Principal (Branch Main)
- **Branch**: `main` (ou outra branch)
- **Porta**: 80/443 (nginx)
- **Função**: Aplicação web completa
- **Broadcasting**: Conecta-se ao servidor Reverb

## 📝 Configuração na Aplicação Principal

Na sua aplicação principal (branch main), configure para usar este servidor Reverb:

```env
# .env da aplicação principal
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=reverb-app-id
REVERB_APP_KEY=reverb-app-key
REVERB_APP_SECRET=reverb-app-secret
REVERB_HOST=seu-dominio-reverb.com
REVERB_PORT=8080
REVERB_SCHEME=https
```

## 🔐 Segurança

### CORS (Cross-Origin Resource Sharing)

Para permitir que sua aplicação principal se conecte ao servidor Reverb, configure os `allowed_origins` no arquivo `config/reverb.php`:

```php
'allowed_origins' => [
    'https://seu-dominio-principal.com',
    'https://www.seu-dominio-principal.com',
],
```

### Autenticação

O Reverb usa as mesmas credenciais de broadcasting da aplicação Laravel. Certifique-se de que:

1. `REVERB_APP_ID`, `REVERB_APP_KEY` e `REVERB_APP_SECRET` são os mesmos em ambas as aplicações
2. As rotas de autenticação (`routes/channels.php`) estão configuradas na aplicação principal
3. O servidor Reverb tem acesso ao Redis/database se necessário para autenticação

## 📊 Monitoring

### Logs

Os logs do Reverb aparecerão no stdout/stderr do container. No Coolify, você pode visualizar os logs através da interface.

### Health Check

O Reverb não tem um endpoint de health check nativo, mas você pode criar um script simples:

```bash
# Verificar se o servidor está rodando
curl -I http://seu-servidor-reverb:8080
```

## 🚨 Troubleshooting

### Porta já em uso

Se a porta 8080 estiver em uso, altere no `.env`:
```env
REVERB_SERVER_PORT=8081
REVERB_PORT=8081
```

### Conexão recusada

1. Verifique se o servidor Reverb está rodando
2. Verifique se o firewall permite a porta configurada
3. Verifique se `REVERB_HOST` está correto
4. Verifique se `REVERB_SERVER_HOST=0.0.0.0` está configurado

### Erro de autenticação

1. Verifique se as credenciais (`REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`) são idênticas em ambas as aplicações
2. Verifique se `allowed_origins` inclui o domínio da aplicação principal

## 📚 Recursos

- [Documentação Laravel Reverb](https://laravel.com/docs/reverb)
- [Laravel Broadcasting](https://laravel.com/docs/broadcasting)
- [Branch Principal](../main/README.md)

