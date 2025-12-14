# Deploy do Servidor Reverb - Coolify/Dockploy

Este guia descreve como fazer deploy do servidor Reverb standalone usando Nixpacks no Coolify/Dockploy.

## 📦 Pré-requisitos

- Conta no Coolify/Dockploy
- Acesso ao repositório Git
- Domínio ou subdomínio para o servidor Reverb (opcional, mas recomendado)

## 🚀 Passo a Passo

### 1. Criar Nova Aplicação no Coolify

1. Acesse o painel do Coolify/Dockploy
2. Clique em "New Resource" > "Application"
3. Conecte seu repositório Git
4. **Selecione a branch `reverb`**
5. Configure o build pack como **Nixpacks** (PHP)

### 2. Configurar Variáveis de Ambiente

Configure as seguintes variáveis de ambiente no painel do Coolify:

```env
# Configurações Básicas do Laravel
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_ENV=production
APP_DEBUG=false
APP_URL=https://reverb.seudominio.com

# Credenciais do Reverb (use as mesmas na aplicação principal)
REVERB_APP_ID=reverb-app-id-unico
REVERB_APP_KEY=chave-publica-reverb
REVERB_APP_SECRET=chave-secreta-reverb

# Configurações do Servidor
REVERB_HOST=reverb.seudominio.com
REVERB_PORT=8080
REVERB_SCHEME=https

# Servidor Reverb
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Redis (se usar scaling)
REVERB_SCALING_ENABLED=false
REDIS_HOST=redis-host
REDIS_PASSWORD=senha-redis
REDIS_PORT=6379
```

### 3. Configurar Porta

No painel do Coolify, configure a porta do container:
- **Porta Interna**: `8080` (ou a porta configurada em `REVERB_SERVER_PORT`)
- **Porta Externa**: Deixe o Coolify atribuir ou configure manualmente

### 4. Configurar Domínio (Opcional mas Recomendado)

1. No painel do Coolify, vá em "Domains"
2. Adicione o domínio/subdomínio do servidor Reverb (ex: `reverb.seudominio.com`)
3. Configure o proxy reverso para a porta interna `8080`

### 5. Deploy

1. Salve as configurações
2. Clique em "Deploy"
3. Aguarde o build completar
4. Verifique os logs para confirmar que o Reverb iniciou corretamente

## 🔍 Verificação Pós-Deploy

### Verificar se o Servidor Está Rodando

Nos logs do container, você deve ver algo como:

```
Starting Reverb server...
Reverb server started on 0.0.0.0:8080
```

### Testar Conexão

Você pode testar se o servidor está respondendo:

```bash
curl -I http://reverb.seudominio.com:8080
```

## ⚙️ Configuração na Aplicação Principal

Na sua aplicação principal (branch `main`), configure para se conectar a este servidor:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=reverb-app-id-unico  # Mesmo valor do servidor
REVERB_APP_KEY=chave-publica-reverb  # Mesmo valor do servidor
REVERB_APP_SECRET=chave-secreta-reverb  # Mesmo valor do servidor
REVERB_HOST=reverb.seudominio.com  # Endereço do servidor Reverb
REVERB_PORT=8080
REVERB_SCHEME=https
```

## 🔐 Segurança

### CORS

Certifique-se de configurar os `allowed_origins` no arquivo `config/reverb.php`:

```php
'allowed_origins' => [
    'https://seudominio.com',
    'https://www.seudominio.com',
],
```

### Firewall

Configure o firewall para permitir apenas:
- Porta 8080 (WebSocket)
- IPs dos seus servidores de aplicação (se necessário)

### HTTPS/WSS

Para produção, sempre use HTTPS/WSS:
- Configure SSL/TLS no Coolify para o domínio do Reverb
- Use `REVERB_SCHEME=https` e `REVERB_PORT=443`

## 📊 Monitoring

### Logs

Visualize os logs no painel do Coolify:
- Acesse a aplicação > "Logs"
- Os logs do Reverb aparecerão em tempo real

### Health Checks

O Coolify pode configurar health checks automaticamente, mas o Reverb não tem endpoint de health. Você pode:

1. Monitorar os logs
2. Usar um script externo para verificar se a porta está aberta
3. Monitorar conexões ativas através de métricas customizadas

## 🚨 Troubleshooting

### Servidor não inicia

**Problema**: O servidor não inicia após o deploy

**Soluções**:
1. Verifique os logs para erros específicos
2. Verifique se `APP_KEY` está configurado
3. Verifique se as variáveis do Reverb estão corretas
4. Verifique se a porta 8080 está disponível

### Conexão recusada

**Problema**: A aplicação principal não consegue conectar ao Reverb

**Soluções**:
1. Verifique se o domínio do Reverb está correto
2. Verifique se o firewall permite a conexão
3. Verifique se `REVERB_SERVER_HOST=0.0.0.0`
4. Verifique se as credenciais são idênticas em ambas as aplicações

### Erro de CORS

**Problema**: Erro de CORS ao conectar

**Soluções**:
1. Configure `allowed_origins` em `config/reverb.php`
2. Verifique se o domínio da aplicação principal está na lista
3. Limpe o cache: `php artisan config:clear`

### Porta em conflito

**Problema**: Porta 8080 já está em uso

**Soluções**:
1. Altere `REVERB_SERVER_PORT` para outra porta (ex: 8081)
2. Atualize a configuração do Coolify para usar a nova porta
3. Atualize `REVERB_PORT` na aplicação principal

## 📚 Recursos Adicionais

- [README-REVERB.md](./README-REVERB.md) - Documentação completa da branch
- [Documentação Laravel Reverb](https://laravel.com/docs/reverb)
- [Documentação Coolify](https://coolify.io/docs)

