# 🧪 Guia de Teste - Melhorias de Chamadas WebRTC

## Como Testar as Melhorias

### 1️⃣ Preparação

Abra o console do navegador (F12) para ver os logs detalhados durante os testes.

---

### 2️⃣ Teste Básico de Chamada

**Objetivo**: Verificar se a chamada conecta normalmente

**Passos**:
1. Abra dois navegadores (ou abas anônimas) com usuários diferentes
2. Inicie uma chamada de voz ou vídeo
3. Aceite a chamada no outro lado
4. **Observe no console**:
   ```
   [CallManager] 🔗 PeerConnection criada com config otimizada
   [CallManager] 📡 Transceivers bidirecionais configurados
   [CallManager] ✓ Sinal enviado: offer (tentativa 1)
   [CallManager] 🧊 ICE candidate gerado...
   [CallManager] 🎵 Track remoto recebido: audio
   [CallManager] 🌐 ICE Connection State: connected
   [CallManager] ✓✓✓ Conexão estabelecida com sucesso!
   ```

**Resultado Esperado**:
- ✅ Chamada conecta em menos de 5 segundos
- ✅ Áudio/vídeo funciona em ambas as direções
- ✅ Toast "Conectado!" aparece

---

### 3️⃣ Teste de Retry Automático

**Objetivo**: Verificar se sinalizações são reenviadas automaticamente

**Como Simular**:
1. No console, execute antes de fazer a chamada:
   ```javascript
   // Simular falha temporária de rede
   const originalFetch = window.fetch;
   let failCount = 0;
   window.fetch = function(...args) {
       if (failCount < 2 && args[0].includes('call-signal')) {
           failCount++;
           return Promise.reject(new Error('Simulando falha de rede'));
       }
       return originalFetch.apply(this, args);
   };
   ```

2. Inicie a chamada
3. **Observe no console**:
   ```
   [CallManager] ✗ Erro ao enviar sinal offer (tentativa 1)
   [CallManager] ↻ Tentando novamente em 1000ms...
   [CallManager] ✗ Erro ao enviar sinal offer (tentativa 2)
   [CallManager] ↻ Tentando novamente em 1500ms...
   [CallManager] ✓ Sinal enviado: offer (tentativa 3)
   ```

**Resultado Esperado**:
- ✅ Sistema tenta até 3 vezes automaticamente
- ✅ Delay aumenta entre tentativas (exponential backoff)
- ✅ Chamada conecta após as falhas temporárias

---

### 4️⃣ Teste de Heartbeat

**Objetivo**: Verificar se o sistema detecta conexões "mortas"

**Passos**:
1. Estabeleça uma chamada normalmente
2. No console da máquina que RECEBEU a chamada, execute:
   ```javascript
   // Simular perda de conexão
   CallManager.state.peerConnection.getReceivers().forEach(r => {
       if (r.track) r.track.enabled = false;
   });
   ```

3. Aguarde ~20 segundos
4. **Observe no console do outro lado**:
   ```
   [CallManager] 💀 Conexão parece morta (sem dados) 15000ms
   [CallManager] 🔄 Tentativa de reconexão 1/5 em 1500ms
   [CallManager] 🔄 Iniciando ICE restart...
   ```

**Resultado Esperado**:
- ✅ Heartbeat detecta falta de dados
- ✅ Reconexão automática inicia
- ✅ Toast "Reconectando..." aparece

---

### 5️⃣ Teste de Reconexão com Exponential Backoff

**Objetivo**: Verificar múltiplas tentativas de reconexão

**Como Simular**:
1. Durante uma chamada ativa, execute no console:
   ```javascript
   // Forçar estado de falha
   CallManager.state.peerConnection.close();
   CallManager.state.peerConnection = new RTCPeerConnection(CallManager.config);
   CallManager.createPeerConnection();
   CallManager.state.connectionState = 'failed';
   CallManager.attemptReconnect();
   ```

2. **Observe no console**:
   ```
   [CallManager] 🔄 Tentativa de reconexão 1/5 em 1500ms
   [CallManager] 🔄 Iniciando ICE restart...
   [CallManager] ✓ ICE restart iniciado com sucesso
   ```

**Resultado Esperado**:
- ✅ Reconexão inicia automaticamente
- ✅ Delays aumentam: 1.5s → 2.25s → 3.37s → 5.06s → 7.59s
- ✅ Até 5 tentativas antes de desistir

---

### 6️⃣ Teste de Timeout de Conexão

**Objetivo**: Verificar se o sistema detecta timeout ao conectar

**Como Simular**:
1. Antes de iniciar chamada, execute:
   ```javascript
   CallManager.config.connectionTimeout = 5000; // 5 segundos apenas
   ```

2. Inicie a chamada mas NÃO aceite do outro lado
3. Aguarde 5 segundos
4. **Observe no console**:
   ```
   [CallManager] ⏱ Timeout ao estabelecer conexão
   ```

**Resultado Esperado**:
- ✅ Timeout detectado após 5 segundos
- ✅ Toast de erro aparece
- ✅ Tentativa de reconexão inicia

---

### 7️⃣ Teste de Transceivers Bidirecionais

**Objetivo**: Garantir áudio/vídeo em ambas direções

**Passos**:
1. Estabeleça uma chamada de vídeo
2. No console de AMBOS os lados, execute:
   ```javascript
   CallManager.state.peerConnection.getTransceivers().forEach(t => {
       console.log(`${t.mid}: direction = ${t.direction}`);
   });
   ```

**Resultado Esperado**:
- ✅ Todos os transceivers mostram `direction = "sendrecv"`
- ✅ Áudio e vídeo funcionam em ambas direções

---

### 8️⃣ Teste de Limpeza de Recursos

**Objetivo**: Verificar se todos os timers são limpos

**Passos**:
1. Inicie uma chamada
2. No console, antes de encerrar:
   ```javascript
   console.log('Timers ativos:', {
       heartbeat: CallManager.state.heartbeatInterval,
       connectionTimeout: CallManager.state.connectionTimeoutId,
       callTimeout: CallManager.state.callTimeoutId,
       timer: CallManager.state.timerInterval,
       stats: CallManager.state.statsInterval
   });
   ```

3. Encerre a chamada
4. Execute novamente o código acima

**Resultado Esperado**:
- ✅ Antes de encerrar: todos os IDs presentes
- ✅ Depois de encerrar: todos null
- ✅ Console mostra: `[CallManager] 🧹 Limpando chamada...`
- ✅ Console mostra: `[CallManager] ✓ Chamada limpa`

---

### 9️⃣ Teste de Rede Lenta

**Objetivo**: Verificar comportamento em redes lentas

**Passos**:
1. Abra DevTools → Network → Throttling → Slow 3G
2. Inicie uma chamada
3. Observe o processo de conexão

**Resultado Esperado**:
- ✅ Chamada ainda conecta (pode demorar mais)
- ✅ Timeout de 60s é suficiente
- ✅ Heartbeat monitora a conexão
- ✅ Sem erros de timeout prematuro

---

### 🔟 Teste de Chamadas Consecutivas

**Objetivo**: Verificar se múltiplas chamadas funcionam sem problemas

**Passos**:
1. Faça uma chamada
2. Encerre após 10 segundos
3. Imediatamente faça outra chamada
4. Repita 3 vezes

**Resultado Esperado**:
- ✅ Todas as chamadas conectam normalmente
- ✅ Sem vazamento de memória
- ✅ Sem timers órfãos
- ✅ Cada chamada começa "limpa"

---

## 📊 Checklist de Validação

Após todos os testes, verifique:

- [ ] Chamadas conectam na primeira tentativa em rede estável
- [ ] Retry automático funciona em caso de falhas temporárias
- [ ] Heartbeat detecta conexões mortas e reconecta
- [ ] Exponential backoff aumenta delays corretamente
- [ ] Transceivers são bidirecionais (sendrecv)
- [ ] Timeouts são adequados (60s para atender, 30s para conectar)
- [ ] Todos os timers são limpos ao encerrar
- [ ] Logging é claro e útil
- [ ] Toasts informam o usuário adequadamente
- [ ] Múltiplas chamadas consecutivas funcionam

---

## 🐛 Problemas Conhecidos

Se encontrar algum problema:

1. **Verifique o console** para mensagens de erro
2. **Copie os logs** do CallManager
3. **Anote os passos** para reproduzir
4. **Verifique a rede** (latência, perda de pacotes)

---

## 💡 Dicas

- Use **abas anônimas** para simular usuários diferentes
- **Chrome DevTools** tem melhores ferramentas WebRTC
- Digite `chrome://webrtc-internals/` para ver detalhes técnicos
- Logs com emojis facilitam identificação rápida de eventos

---

**Bons testes! 🚀**
