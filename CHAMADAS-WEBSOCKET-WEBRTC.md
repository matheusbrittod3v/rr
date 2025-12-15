# 📞 Sistema de Chamadas de Voz/Vídeo - WebSocket + WebRTC

Sistema completo de chamadas de voz e vídeo integrado com WebSocket (Reverb) para sinalização e WebRTC para comunicação de mídia.

## 🏗️ Arquitetura

### Fluxo de Comunicação

1. **Sinalização via WebSocket (Reverb)**
   - Início da chamada (offer/answer)
   - Troca de ICE candidates
   - Controle de chamada (encerrar, etc.)

2. **Mídia via WebRTC**
   - Áudio e vídeo em tempo real
   - Conexão peer-to-peer
   - Máxima performance e baixa latência

## 📁 Arquivos Criados/Modificados

### 1. `public/js/webrtc-manager.js`
Gerenciador WebRTC completo com:
- ✅ Inicialização de chamadas (vídeo/voz)
- ✅ Aceitar chamadas recebidas
- ✅ Toggle câmera (ligar/desligar)
- ✅ Toggle áudio (mutar/desmutar)
- ✅ Trocar câmera (frente/trás)
- ✅ Minimizar/maximizar chamada
- ✅ Encerrar chamada
- ✅ Integração com WebSocket (Reverb)

### 2. `app/Http/Controllers/ChatController.php`
Adicionado método `sendCallSignal()`:
- Recebe sinais de chamada
- Faz broadcast via Reverb (WebSocket)
- Valida autenticação

### 3. `routes/web.php`
Adicionada rota:
```php
Route::post('/{roomCode}/call-signal', [ChatController::class, 'sendCallSignal'])
```

### 4. `resources/views/chat/room.blade.php`
Interface completa de chamadas:
- ✅ Botões de chamada (vídeo/voz) no header
- ✅ Overlay de chamada (tela cheia)
- ✅ Vídeo local (Picture-in-Picture)
- ✅ Vídeo remoto (tela principal)
- ✅ Controles: câmera, áudio, flip, minimizar, encerrar
- ✅ Chamada minimizada (canto inferior direito)
- ✅ Timer de duração da chamada

## 🎮 Funcionalidades

### Controles Disponíveis

1. **Toggle Câmera** 📹
   - Liga/desliga a câmera durante a chamada
   - Ícone muda dinamicamente

2. **Toggle Áudio** 🎤
   - Muta/desmuta o microfone
   - Ícone muda dinamicamente

3. **Trocar Câmera** 🔄
   - Alterna entre câmera frontal e traseira
   - Funciona durante a chamada

4. **Minimizar** 📱
   - Minimiza chamada para canto inferior direito
   - Similar ao WhatsApp
   - Pode maximizar novamente

5. **Encerrar Chamada** ❌
   - Botão vermelho para encerrar
   - Limpa recursos e notifica o outro usuário

## 🔧 Como Funciona

### Iniciar Chamada

1. Usuário clica em "Chamada de Vídeo" ou "Chamada de Voz"
2. Sistema solicita permissões de câmera/microfone
3. WebRTC cria oferta (offer) e envia via WebSocket
4. Outro usuário recebe oferta e aceita automaticamente
5. Conexão WebRTC é estabelecida
6. Mídia (áudio/vídeo) flui via WebRTC

### Sinalização (WebSocket)

- **Offer**: Iniciador envia proposta de conexão
- **Answer**: Receptor responde à proposta
- **Candidate**: Troca de endereços ICE para NAT traversal
- **Bye**: Sinal de encerramento

### Comunicação de Mídia (WebRTC)

- Conexão peer-to-peer direta
- STUN servers para NAT traversal
- TURN servers podem ser adicionados para casos complexos

## 🚀 Uso

### Para o Usuário

1. **Iniciar Chamada de Vídeo:**
   - Clique no ícone de vídeo no header do chat
   - Permita acesso à câmera e microfone
   - Aguarde o outro usuário aceitar

2. **Iniciar Chamada de Voz:**
   - Clique no ícone de telefone no header
   - Permita acesso ao microfone
   - Aguarde o outro usuário aceitar

3. **Durante a Chamada:**
   - Use os controles na parte inferior
   - Minimize para continuar usando o chat
   - Encerre quando terminar

## ⚙️ Configuração

### STUN/TURN Servers

O sistema usa servidores STUN públicos do Google por padrão. Para produção, considere:

1. **Adicionar servidores TURN** (para NATs complexos):
```javascript
this.rtcConfig = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { 
            urls: 'turn:seu-turn-server.com:3478',
            username: 'usuario',
            credential: 'senha'
        }
    ]
};
```

2. **Serviços TURN recomendados:**
   - Twilio STUN/TURN
   - Xirsys
   - Metered TURN

## 🔍 Debug

### Console do Navegador

O sistema loga todas as operações:
- `[WebRTC]` - Operações WebRTC
- `[Call]` - Operações de chamada
- `[Echo]` - Operações WebSocket

### Verificar Conexão

1. Abra o console do navegador (F12)
2. Inicie uma chamada
3. Verifique os logs:
   - Estado da conexão WebRTC
   - Sinais enviados/recebidos
   - Erros (se houver)

## ⚠️ Requisitos

### Navegador

- Chrome/Edge (recomendado)
- Firefox
- Safari (iOS 11+)
- Opera

### Permissões

- Câmera (para chamadas de vídeo)
- Microfone (para todas as chamadas)

### HTTPS

WebRTC requer HTTPS em produção (exceto localhost).

## 🐛 Troubleshooting

### "getUserMedia não está disponível"
- Verifique se está usando HTTPS
- Verifique permissões do navegador

### "Conexão falhou"
- Verifique firewall/NAT
- Adicione servidores TURN
- Verifique logs do console

### "Chamada não conecta"
- Verifique se WebSocket está funcionando
- Verifique se Reverb está rodando
- Verifique logs do servidor

## 📝 Próximos Passos

1. Adicionar notificação de chamada recebida
2. Adicionar histórico de chamadas
3. Adicionar gravação de chamadas (com consentimento)
4. Adicionar compartilhamento de tela
5. Adicionar chat durante a chamada

