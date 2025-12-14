# 🔧 Como Corrigir Erro 404 no Reverb

## ⚠️ Problema

O Reverb está retornando 404 quando acessado via HTTP.

## ✅ Solução

**O Reverb é um servidor WebSocket, não HTTP!** Ele não responde a requisições HTTP GET normais (como `/` ou `/health`), o que pode causar 404 em health checks.

### O que foi corrigido:

1. **`.nixpacks.toml` atualizado** com configuração explícita de host e porta
2. Adicionada variável `REVERB_SERVER_PORT` para facilitar configuração

## 🔍 Como Verificar se o Reverb Está Funcionando

### 1. Verificar se o processo está rodando

O Reverb deve estar rodando como processo PHP. No Coolify/Dockploy, verifique os logs do container:

```bash
# No painel do Coolify/Dockploy, veja os logs do serviço Reverb
# Você deve ver algo como:
# "Starting Reverb server..."
# "Reverb server started on 0.0.0.0:8080"
```

### 2. Testar conexão WebSocket (não HTTP)

O Reverb não responde a requisições HTTP normais. Use um cliente WebSocket:

**JavaScript/Node.js:**
```javascript
const WebSocket = require('ws');

const ws = new WebSocket('ws://seu-dominio:8080/app/REVERB_APP_KEY', {
  headers: {
    'Host': 'seu-dominio'
  }
});

ws.on('open', () => {
  console.log('✅ Conectado ao Reverb!');
});

ws.on('error', (error) => {
  console.error('❌ Erro:', error);
});
```

**PHP (script de teste):**
```php
<?php
$host = getenv('REVERB_HOST') ?: 'localhost';
$port = (int)(getenv('REVERB_SERVER_PORT') ?: 8080);

$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$fp = @fsockopen($host, $port, $errno, $errstr, 5);
if ($fp) {
    echo "✅ Porta $port está aberta!\n";
    fclose($fp);
} else {
    echo "❌ Erro: $errstr ($errno)\n";
}
```

### 3. Verificar variáveis de ambiente

No Coolify/Dockploy, certifique-se de ter configurado:

```env
# Obrigatórias
APP_KEY=base64:...
REVERB_APP_ID=seu-app-id
REVERB_APP_KEY=seu-app-key
REVERB_APP_SECRET=seu-app-secret
REVERB_HOST=seu-dominio-reverb.com

# Opcionais (padrões estão OK)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_PORT=443
REVERB_SCHEME=https
```

## 🚨 Erros Comuns

### Erro 404 em Health Check

**Causa:** O Coolify/Dockploy pode estar fazendo health check HTTP que o Reverb não responde.

**Solução:** 
- Configure o health check para testar a porta TCP diretamente
- Ou desabilite o health check HTTP e use apenas verificação de processo

### Porta já em uso

**Causa:** Outro processo está usando a porta 8080.

**Solução:**
```env
REVERB_SERVER_PORT=8081  # Use outra porta
```

### Variáveis de ambiente não configuradas

**Causa:** Faltam variáveis `REVERB_*` obrigatórias.

**Solução:** Configure todas as variáveis listadas acima no painel do Coolify/Dockploy.

## 📝 Notas Importantes

1. **O Reverb é WebSocket, não HTTP**: Não espere que ele responda a `GET /` com HTML
2. **Porta configurada**: Certifique-se de que a porta configurada no `.env` (`REVERB_SERVER_PORT`) está aberta e acessível
3. **Health Check**: Se usar health check, configure para testar conexão TCP na porta, não HTTP GET
4. **Logs**: Sempre verifique os logs do container para ver se o Reverb iniciou corretamente

## ✅ Verificação Final

Para confirmar que está funcionando:

1. ✅ Processo está rodando (veja logs: "Reverb server started")
2. ✅ Porta está aberta (teste com `nc`, `telnet` ou script PHP)
3. ✅ Conexão WebSocket funciona (teste com cliente WebSocket)
4. ✅ Variáveis de ambiente configuradas corretamente

