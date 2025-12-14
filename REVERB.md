# Laravel Reverb - Guia de Configuração

## O que é Laravel Reverb?

Laravel Reverb é o servidor WebSocket oficial do Laravel, desenvolvido pela equipe do Laravel. Ele permite comunicação em tempo real bidirecional entre o servidor e os clientes, sem depender de serviços externos como Pusher.

## Instalação

O Reverb já está instalado no projeto. O pacote `laravel/reverb` está incluído no `composer.json`.

## Configuração Básica

### 1. Variáveis de Ambiente

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# Usar Reverb como driver de broadcasting
BROADCAST_CONNECTION=reverb

# Credenciais da aplicação Reverb
REVERB_APP_ID=app-id-gerado
REVERB_APP_KEY=app-key-gerado
REVERB_APP_SECRET=app-secret-gerado

# Configurações do servidor
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Para produção com HTTPS
# REVERB_SCHEME=https
# REVERB_PORT=443
```

### 2. Gerar Credenciais

Você pode gerar credenciais únicas usando:

```bash
php artisan reverb:install
```

Ou defina manualmente no `.env`:
- `REVERB_APP_ID`: Um identificador único para sua aplicação
- `REVERB_APP_KEY`: Chave pública para clientes se conectarem
- `REVERB_APP_SECRET`: Chave secreta para autenticação no servidor

### 3. Iniciar o Servidor Reverb

#### Desenvolvimento Local

```bash
php artisan reverb:start
```

O servidor iniciará na porta configurada (padrão: 8080).

#### Produção

Para produção, você precisa executar o Reverb como um processo em background. Opções:

**Opção 1: Supervisor (Recomendado)**

Crie um arquivo `/etc/supervisor/conf.d/reverb.conf`:

```ini
[program:reverb]
process_name=%(program_name)s
command=php /caminho/para/seu/projeto/artisan reverb:start
autostart=true
autorestart=true
user=seu-usuario
redirect_stderr=true
stdout_logfile=/caminho/para/logs/reverb.log
```

Depois execute:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
```

**Opção 2: Systemd**

Crie `/etc/systemd/system/reverb.service`:

```ini
[Unit]
Description=Laravel Reverb Server
After=network.target

[Service]
Type=simple
User=seu-usuario
WorkingDirectory=/caminho/para/seu/projeto
ExecStart=/usr/bin/php artisan reverb:start
Restart=always

[Install]
WantedBy=multi-user.target
```

Depois execute:
```bash
sudo systemctl daemon-reload
sudo systemctl enable reverb
sudo systemctl start reverb
```

## Configuração do Cliente (Frontend)

### JavaScript (Laravel Echo)

No seu frontend, você precisa configurar o Laravel Echo para usar o Reverb:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### Variáveis de Ambiente do Frontend (.env ou Vite)

```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Escalando com Redis

Se você tem múltiplos servidores Reverb, pode habilitar o scaling:

```env
REVERB_SCALING_ENABLED=true
REVERB_SCALING_CHANNEL=reverb

# Configurações do Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0
```

## Configuração no Nixpacks/Coolify

Para executar o Reverb junto com sua aplicação no Coolify, você tem algumas opções:

### Opção 1: Container Separado

Crie um novo serviço no Coolify apenas para o Reverb com o mesmo código, mas com comando:

```bash
php artisan reverb:start
```

### Opção 2: Supervisor no mesmo container

Use o Supervisor (já configurado) para executar nginx, PHP-FPM, queue worker e Reverb juntos.

## Rotas de Broadcasting

No Laravel, você pode definir rotas de broadcasting em `routes/channels.php`:

```php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{roomId}', function ($user, $roomId) {
    return ['id' => $user->id, 'name' => $user->name];
});
```

## Usando no Código

### Broadcasting de Eventos

```php
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('chat.1');
    }
}
```

### Escutando no Frontend

```javascript
window.Echo.channel('chat.1')
    .listen('MessageSent', (e) => {
        console.log(e.message);
    });
```

## Troubleshooting

### Porta já em uso

Se a porta 8080 estiver em uso, altere no `.env`:
```env
REVERB_PORT=8081
REVERB_SERVER_PORT=8081
```

### Conexão recusada

- Verifique se o servidor Reverb está rodando
- Verifique se o firewall permite a porta configurada
- Verifique se `REVERB_HOST` está correto

### Erro de CORS

O Reverb permite configurar origens permitidas em `config/reverb.php`:
```php
'allowed_origins' => ['https://seu-dominio.com'],
```

## Recursos

- [Documentação Oficial do Laravel Reverb](https://laravel.com/docs/reverb)
- [Laravel Broadcasting](https://laravel.com/docs/broadcasting)
- [Laravel Echo](https://laravel.com/docs/broadcasting#client-side-installation)

