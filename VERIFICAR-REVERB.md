# 🔍 Como Verificar se o Reverb Iniciou

## 📋 Verificações Básicas

### 0. Teste Rápido com Script PHP

**Versão Completa:**
```bash
php test-reverb.php
```

**Versão Simples (apenas testa se porta está aberta):**
```bash
php test-reverb-simple.php
```

Os scripts testam:
- ✅ Conexão TCP na porta do Reverb
- ✅ Resposta HTTP do servidor
- ✅ Handshake WebSocket básico
- ✅ Variáveis de ambiente

### 1. Verificar Logs do Container/Servidor

#### No Coolify/Dockploy:
1. Acesse o painel do Coolify
2. Vá para sua aplicação Reverb
3. Clique em **"Logs"**
4. Procure por uma mensagem similar a:

```
Starting Reverb server...
Reverb server started on 0.0.0.0:8080
```

#### Localmente:
```bash
# Se iniciou manualmente, verá a saída direto no terminal
php artisan reverb:start
```

### 2. Verificar se a Porta Está Aberta

#### Teste de Conexão (de outro servidor/máquina):

```bash
# Testar se a porta está respondendo
curl -I http://reverb.seudominio.com:8080

# Ou usando telnet
telnet reverb.seudominio.com 8080

# Ou usando nc (netcat)
nc -zv reverb.seudominio.com 8080
```

**Resposta esperada:** Conexão estabelecida (não deve retornar erro)

### 3. Verificar Processo em Execução

#### Dentro do Container:

```bash
# Conectar ao container
docker exec -it <container-id> bash

# Verificar se o processo está rodando
ps aux | grep reverb

# Ou procurar pelo processo PHP
ps aux | grep "php artisan reverb"
```

**Resposta esperada:** Deve mostrar um processo PHP rodando `artisan reverb:start`

### 4. Testar Conexão WebSocket

#### Usando JavaScript no Browser Console:

```javascript
// Testar conexão WebSocket diretamente
const ws = new WebSocket('ws://reverb.seudominio.com:8080/app/reverb-app-key');

ws.onopen = () => {
    console.log('✅ Conexão WebSocket estabelecida!');
};

ws.onerror = (error) => {
    console.error('❌ Erro na conexão:', error);
};

ws.onclose = () => {
    console.log('Conexão fechada');
};
```

#### Usando cURL (para testar upgrade de conexão):

```bash
curl -i -N \
  -H "Connection: Upgrade" \
  -H "Upgrade: websocket" \
  -H "Sec-WebSocket-Version: 13" \
  -H "Sec-WebSocket-Key: test" \
  http://reverb.seudominio.com:8080/app/reverb-app-key
```

### 5. Verificar Variáveis de Ambiente

Certifique-se de que todas as variáveis necessárias estão configuradas:

```bash
# Dentro do container
env | grep REVERB
env | grep APP_KEY
```

**Deve mostrar:**
```
REVERB_APP_ID=reverb-app-id
REVERB_APP_KEY=reverb-app-key
REVERB_APP_SECRET=reverb-app-secret
REVERB_HOST=reverb.seudominio.com
REVERB_PORT=8080
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
APP_KEY=base64:...
```

## 🟢 Sinais de Sucesso

### ✅ Servidor Funcionando Corretamente:

1. **Nos logs você vê:**
   ```
   [server:info] Server starting on port 8080
   Reverb server started on 0.0.0.0:8080
   ```

2. **A porta está respondendo:**
   - Conexão estabelecida ao tentar conectar na porta 8080

3. **Processo está rodando:**
   - `ps aux | grep reverb` mostra o processo ativo

4. **WebSocket conecta:**
   - Conexão WebSocket estabelecida com sucesso

## 🔴 Sinais de Problema

### ❌ Servidor Não Iniciou:

1. **Nos logs você vê:**
   ```
   [laravel:error] Your app key is not set!
   ```
   **Solução:** Configure `APP_KEY` nas variáveis de ambiente

2. **Erro de porta:**
   ```
   Address already in use
   ```
   **Solução:** Altere `REVERB_SERVER_PORT` para outra porta ou libere a porta 8080

3. **Erro de conexão:**
   ```
   Connection refused
   ```
   **Soluções:**
   - Verifique se o servidor está rodando
   - Verifique firewall/proxy
   - Verifique se `REVERB_SERVER_HOST=0.0.0.0`

4. **Processo não encontrado:**
   ```
   No process found
   ```
   **Solução:** Verifique os logs para erros durante a inicialização

## 🧪 Script de Teste Completo

Crie um arquivo `test-reverb.sh`:

```bash
#!/bin/bash

REVERB_HOST="reverb.seudominio.com"
REVERB_PORT="8080"

echo "🔍 Testando servidor Reverb em ${REVERB_HOST}:${REVERB_PORT}"

# Teste 1: Porta aberta
echo -n "1. Testando porta... "
if nc -zv ${REVERB_HOST} ${REVERB_PORT} 2>&1 | grep -q "succeeded"; then
    echo "✅ Porta aberta"
else
    echo "❌ Porta não responde"
    exit 1
fi

# Teste 2: Processo rodando (se tiver acesso ao container)
echo -n "2. Verificando processo... "
if ps aux | grep -q "[p]hp artisan reverb"; then
    echo "✅ Processo ativo"
else
    echo "⚠️  Não foi possível verificar processo"
fi

# Teste 3: WebSocket (requer node/npm)
echo -n "3. Testando WebSocket... "
# Este teste requer uma ferramenta específica de WebSocket
echo "⚠️  Use o teste JavaScript no browser"

echo ""
echo "✅ Testes básicos concluídos!"
```

## 📱 Monitoramento em Produção

### No Coolify/Dockploy:

1. **Logs em Tempo Real:**
   - Acesse a aplicação > Logs
   - Os logs são atualizados em tempo real

2. **Métricas do Container:**
   - Verifique CPU/Memória do container
   - Se estiver usando recursos, provavelmente está rodando

3. **Health Check:**
   - O Reverb não tem endpoint de health nativo
   - Mas você pode criar um script que verifica a porta

## 🔧 Comandos Úteis

### Ver logs do container:
```bash
docker logs <container-id> -f
```

### Reiniciar o servidor Reverb:
```bash
# Se usando supervisor/systemd
sudo systemctl restart reverb

# Se rodando manualmente
# Pare o processo (Ctrl+C) e inicie novamente:
php artisan reverb:start
```

### Verificar configuração:
```bash
php artisan config:show reverb
```

### Testar conexão local:
```bash
# De dentro do container ou servidor
curl http://localhost:8080
```

## 📚 Próximos Passos

Após confirmar que o Reverb está rodando:

1. ✅ Configure sua aplicação principal para se conectar
2. ✅ Teste uma conexão WebSocket completa
3. ✅ Configure autenticação de canais (se necessário)
4. ✅ Configure scaling com Redis (se usar múltiplos servidores)

