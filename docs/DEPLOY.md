# Guia de Deploy no Coolify/Dockploy

Este guia explica como fazer deploy da aplicação Laravel no Coolify/Dockploy usando Nixpacks.

## Pré-requisitos

- Conta no Coolify/Dockploy
- Repositório Git configurado
- Variáveis de ambiente configuradas

## Configuração no Coolify/Dockploy

### 1. Variáveis de Ambiente Obrigatórias

Configure as seguintes variáveis de ambiente no painel do Coolify/Dockploy:

#### Variáveis Essenciais do Laravel
```
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
```

#### Database
**Para MySQL/MariaDB:**
```
DB_CONNECTION=mysql
DB_HOST=seu-host-db
DB_PORT=3306
DB_DATABASE=nome-do-banco
DB_USERNAME=usuario-db
DB_PASSWORD=senha-db
```

**Para PostgreSQL:**
```
DB_CONNECTION=pgsql
DB_HOST=seu-host-db
DB_PORT=5432
DB_DATABASE=nome-do-banco
DB_USERNAME=usuario-db
DB_PASSWORD=senha-db
```

**⚠️ IMPORTANTE:** Certifique-se de que a variável `DB_CONNECTION` está configurada corretamente (mysql ou pgsql). O driver correspondente será usado automaticamente.

#### Cache/Session
```
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis-host
REDIS_PASSWORD=senha-redis
REDIS_PORT=6379
```

#### Broadcasting (se usar WebRTC/WebSockets)
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=seu-app-id
PUSHER_APP_KEY=seu-app-key
PUSHER_APP_SECRET=seu-app-secret
PUSHER_APP_CLUSTER=us2
```

#### Mail (opcional)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu-username
MAIL_PASSWORD=seu-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Variáveis Nixpacks (Opcional)

O arquivo `.nixpacks.toml` já está configurado, mas você pode definir:

```
NIXPACKS_PHP_ROOT_DIR=/app/public
```

Isso já está definido no `.nixpacks.toml`, então não é necessário definir novamente.

### 3. Comandos de Build

O Nixpacks executará automaticamente:

1. **Instalação:**
   - `composer install --no-dev --optimize-autoloader --no-interaction`
   - `npm ci --omit=dev`

2. **Build:**
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
   - `npm run build`

3. **Start:**
   - Comando do Nixpacks para iniciar Nginx + PHP-FPM

### 4. Comandos de Deploy Adicionais (opcional)

Se precisar executar migrations ou seeders após o deploy, adicione no painel:

```
php artisan migrate --force
php artisan storage:link
```

**⚠️ ATENÇÃO:** Não execute `php artisan key:generate` se já tiver um `APP_KEY` configurado.

### 5. Estrutura de Arquivos

- **Raiz do servidor:** `/app/public` (configurado via `NIXPACKS_PHP_ROOT_DIR`)
- **nginx.template.conf:** Configuração do Nginx para Laravel
- **.nixpacks.toml:** Configuração do build Nixpacks

### 6. Extensões PHP

As extensões PHP necessárias estão definidas no `.nixpacks.toml`:

- bcmath
- ctype
- curl
- gd
- iconv
- intl
- mbstring
- opcache
- pdo
- pdo_mysql (para MySQL/MariaDB)
- pdo_pgsql (para PostgreSQL)
- redis
- zip

**Nota:** Ambas as extensões de banco (pdo_mysql e pdo_pgsql) estão instaladas para suportar MySQL e PostgreSQL. A extensão usada dependerá da variável `DB_CONNECTION` configurada.

### 7. Verificação Pós-Deploy

Após o deploy, verifique:

1. ✅ A aplicação está acessível
2. ✅ Assets estão sendo servidos corretamente (`/build/assets/`)
3. ✅ Storage está linkado (`php artisan storage:link`)
4. ✅ Cache está funcionando
5. ✅ WebRTC/Chat está funcionando (verifique conexões WebSocket/Pusher)

### 8. Troubleshooting

**Erro: "APP_KEY não definida"**
- Certifique-se de definir `APP_KEY` nas variáveis de ambiente

**Erro: "500 Internal Server Error"**
- Verifique os logs: `php artisan log:clear` (se necessário)
- Verifique permissões de storage: `chmod -R 775 storage bootstrap/cache`

**Erro: "could not find driver" (PostgreSQL)**
- Se estiver usando PostgreSQL, a extensão `pdo_pgsql` já está configurada no `.nixpacks.toml`
- Certifique-se de que `DB_CONNECTION=pgsql` está configurado corretamente
- Se estiver usando MySQL, certifique-se de que `DB_CONNECTION=mysql` está configurado

**Erro: "nginx: [emerg] server directive is not allowed here"**
- Isso ocorre quando há um arquivo `nginx.template.conf` mal configurado
- O arquivo foi removido - o Nixpacks agora usa sua configuração padrão
- Se precisar de configuração customizada do nginx, consulte a documentação do Nixpacks

**Warnings sobre variáveis de ambiente não definidas (REVERB_*, PUSHER_*, etc.)**
- Esses são apenas avisos informativos
- Se você não estiver usando broadcasting, pode ignorá-los com segurança
- Para removê-los, defina as variáveis ou remova as referências nos arquivos de configuração

**Assets não carregam**
- Verifique se `npm run build` foi executado
- Verifique se `/public/build` existe e tem os arquivos

**Banco de dados não conecta**
- Verifique variáveis `DB_*`
- Verifique se a variável `DB_CONNECTION` está correta (mysql ou pgsql)
- Verifique se o serviço de banco está acessível da aplicação
- Verifique se a extensão correta está instalada (pdo_mysql para MySQL, pdo_pgsql para PostgreSQL)

**Redis não conecta**
- Verifique variáveis `REDIS_*`
- Verifique se o serviço Redis está rodando

## Estrutura de Deploy

```
/app (raiz do projeto)
├── public/ (raiz do servidor web)
│   ├── index.php
│   ├── build/ (assets compilados)
│   └── storage/ (link simbólico)
├── .nixpacks.toml
├── nginx.template.conf
├── composer.json
└── package.json
```

## Suporte

Para mais informações sobre Nixpacks e Coolify:
- [Documentação Nixpacks](https://nixpacks.com/docs)
- [Documentação Coolify](https://coolify.io/docs)

