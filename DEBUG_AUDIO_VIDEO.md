# 🔍 Debug de Áudio/Vídeo Unidirecional

## ✅ Correções Aplicadas

### 1. **Removidos Transceivers Manuais**
Transceivers criados ANTES dos tracks causavam conflito. Agora o `addTrack()` cria automaticamente com a direção correta (`sendrecv`).

### 2. **Logging Detalhado Adicionado**
Agora você pode ver exatamente o que está acontecendo com os tracks:

```javascript
[CallManager] 📹 Stream local obtido: {audio: 1, video: 1}
[CallManager] ➕ Track local adicionado: {kind: 'audio', enabled: true, readyState: 'live'}
[CallManager] ➕ Track local adicionado: {kind: 'video', enabled: true, readyState: 'live'}
[CallManager] 🎵 Track remoto recebido: {kind: 'audio', enabled: true, readyState: 'live'}
```

### 3. **Play Forçado no Vídeo Remoto**
Alguns navegadores exigem `.play()` explícitamente.

---

## 🧪 Como Testar Agora

### Passo 1: Limpar Cache
```
Ctrl + Shift + Delete → Limpar cache
Ctrl + F5 → Forçar reload
```

### Passo 2: Fazer Chamada de VÍDEO

Abra o console (F12) em AMBOS os lados e observe:

**LADO QUE LIGOU (Initiator):**
```
[CallManager] 📹 Stream local obtido: {audio: 1, video: 1}
[CallManager] ➕ Track local adicionado: {kind: 'audio', enabled: true, ...}
[CallManager] ➕ Track local adicionado: {kind: 'video', enabled: true, ...}
[CallManager] 📤 Offer criado com 2 media lines
[CallManager] 🎵 Track remoto recebido: {kind: 'audio', enabled: true, ...}
[CallManager] 🎵 Track remoto recebido: {kind: 'video', enabled: true, ...}
```

**LADO QUE ACEITOU:**
```
[CallManager] 📹 Stream local obtido (accept): {audio: 1, video: 1}
[CallManager] ➕ Track local adicionado: {kind: 'audio', enabled: true, ...}
[CallManager] ➕ Track local adicionado: {kind: 'video', enabled: true, ...}
[CallManager] 📥 Processando offer pendente...
[CallManager] 🎵 Track remoto recebido: {kind: 'audio', enabled: true, ...}
[CallManager] 🎵 Track remoto recebido: {kind: 'video', enabled: true, ...}
```

### ✅ O Que Procurar

**AMBOS os lados devem mostrar:**
- ✅ `Stream local obtido: {audio: 1, video: 1}`
- ✅ `2 tracks locais adicionados` (audio + video)
- ✅ `2 tracks remotos recebidos` (audio + video)
- ✅ `enabled: true` e `readyState: 'live'`

---

## 🔍 Comando de Debug Manual

No console de qualquer lado, execute:

```javascript
// Ver estado completo dos tracks
console.log('=== DEBUG DE TRACKS ===');

// Tracks locais (enviando)
console.log('TRACKS LOCAIS (Enviando):');
CallManager.state.localStream?.getTracks().forEach(t => {
    console.log(`  ${t.kind}: enabled=${t.enabled}, readyState=${t.readyState}, muted=${t.muted}`);
});

// Senders (o que estamos enviando ao peer)
console.log('\nSENDERS (O que enviamos):');
CallManager.state.peerConnection?.getSenders().forEach(s => {
    if (s.track) {
        console.log(`  ${s.track.kind}: enabled=${s.track.enabled}, readyState=${s.track.readyState}`);
    }
});

// Tracks remotos (recebendo)
console.log('\nTRACKS REMOTOS (Recebendo):');
CallManager.state.remoteStream?.getTracks().forEach(t => {
    console.log(`  ${t.kind}: enabled=${t.enabled}, readyState=${t.readyState}, muted=${t.muted}`);
});

// Receivers (o que estamos recebendo do peer)
console.log('\nRECEIVERS (O que recebemos):');
CallManager.state.peerConnection?.getReceivers().forEach(r => {
    if (r.track) {
        console.log(`  ${r.track.kind}: enabled=${r.track.enabled}, readyState=${r.track.readyState}`);
    }
});

// Transceivers (direção)
console.log('\nTRANSCEIVERS (Direção):');
CallManager.state.peerConnection?.getTransceivers().forEach(t => {
    console.log(`  ${t.mid}: direction=${t.direction}, currentDirection=${t.currentDirection}`);
});
```

### Resultado Esperado

```
=== DEBUG DE TRACKS ===
TRACKS LOCAIS (Enviando):
  audio: enabled=true, readyState=live, muted=false
  video: enabled=true, readyState=live, muted=false

SENDERS (O que enviamos):
  audio: enabled=true, readyState=live
  video: enabled=true, readyState=live

TRACKS REMOTOS (Recebendo):
  audio: enabled=true, readyState=live, muted=false
  video: enabled=true, readyState=live, muted=false

RECEIVERS (O que recebemos):
  audio: enabled=true, readyState=live
  video: enabled=true, readyState=live

TRANSCEIVERS (Direção):
  0: direction=sendrecv, currentDirection=sendrecv
  1: direction=sendrecv, currentDirection=sendrecv
```

**IMPORTANTE**: A direção deve ser `sendrecv` (envia E recebe) em ambos!

---

## ❌ Problemas Possíveis

### Problema 1: Track Remoto com `enabled=false`

**Sintoma**:
```
TRACKS REMOTOS:
  audio: enabled=false, readyState=live ← PROBLEMA!
```

**Solução**: O outro lado mutou o microfone antes de conectar. Peça para desmutar.

---

### Problema 2: Direção `recvonly` ou `sendonly`

**Sintoma**:
```
TRANSCEIVERS:
  0: direction=sendonly ← PROBLEMA! Deveria ser sendrecv
```

**Solução**: Ainda há conflito de transceivers. Execute:
```javascript
// Forçar direção correta
CallManager.state.peerConnection.getTransceivers().forEach(t => {
    t.direction = 'sendrecv';
});
```

---

### Problema 3: Nenhum Track Remoto

**Sintoma**:
```
TRACKS REMOTOS (Recebendo):
  (vazio)
```

**Possíveis causas**:
1. Offer/Answer não incluiu mídia
2. ICE candidates não chegaram
3. Firewall bloqueando RTCP

**Diagnóstico**:
```javascript
// Ver SDP
console.log(atob(CallManager.state.peerConnection.remoteDescription.sdp));
// Deve ter linhas com "m=audio" e "m=video"
```

---

## 🎯 Cenários Específicos

### Vídeo: Quem ligou NÃO vê/escuta quem aceitou

**Provável causa**: Quem aceitou não está enviando tracks

**Verificar** (no lado que ACEITOU):
```javascript
CallManager.state.peerConnection.getSenders().forEach(s => {
    console.log(s.track?.kind, s.track?.enabled);
});
```

**Deve mostrar**: `audio true` e `video true`

---

### Voz: Nenhum dos dois escuta

**Provável causa**: Tracks de áudio não estão fluindo

**Verificar** (AMBOS os lados):
```javascript
// Ver bytes de áudio sendo enviados/recebidos
CallManager.state.peerConnection.getStats().then(stats => {
    stats.forEach(s => {
        if (s.type === 'outbound-rtp' && s.kind === 'audio') {
            console.log('Áudio ENVIANDO:', s.bytesSent, 'bytes');
        }
        if (s.type === 'inbound-rtp' && s.kind === 'audio') {
            console.log('Áudio RECEBENDO:', s.bytesReceived, 'bytes');
        }
    });
});
```

**Se bytes = 0**: Áudio não está fluindo!

---

## 📝 Checklist de Teste

Após limpar cache e recarregar:

### Chamada de Vídeo:
- [ ] Quem ligou VÊ a si mesmo (local video)
- [ ] Quem ligou VÊ quem aceitou (remote video)
- [ ] Quem ligou ESCUTA quem aceitou (remote audio)
- [ ] Quem aceitou VÊ a si mesmo (local video)
- [ ] Quem aceitou VÊ quem ligou (remote video)
- [ ] Quem aceitou ESCUTA quem ligou (remote audio)

### Chamada de Voz:
- [ ] Quem ligou ESCUTA quem aceitou
- [ ] Quem aceitou ESCUTA quem ligou

---

## 🚀 Próximos Passos

1. **Limpe o cache** e força reload (Ctrl+F5)
2. **Faça uma chamada de vídeo**
3. **Execute o comando de debug** em AMBOS os lados
4. **Me envie os resultados** se ainda houver problema

---

**Teste agora e me avise o resultado!** 🎉
