# 🔄 Sistema Híbrido WebSocket + WebRTC

Sistema de chamadas de voz/vídeo com **duas camadas de comunicação** que se alternam automaticamente para máxima performance e confiabilidade.

## 🏗️ Arquitetura Híbrida

### Duas Camadas de Comunicação

1. **WebRTC (Principal)** - Melhor performance
   - Conexão peer-to-peer direta
   - Baixa latência
   - Alta qualidade de mídia
   - Usado quando a conexão está estável

2. **WebSocket (Fallback)** - Máxima confiabilidade
   - Transmissão via servidor (Reverb)
   - Funciona mesmo com NATs complexos
   - Ativado automaticamente quando WebRTC falha
   - Mantém a chamada ativa durante problemas de rede

## 🔄 Como Funciona

### Fluxo Normal (WebRTC)

```
1. Iniciar chamada → WebRTC estabelece conexão
2. Mídia flui via WebRTC (peer-to-peer)
3. WebSocket preparado mas não usado (economia de banda)
4. Monitoramento contínuo da saúde do WebRTC
```

### Fluxo de Fallback (WebSocket)

```
1. WebRTC detecta falhas (3 falhas consecutivas)
2. Sistema alterna automaticamente para WebSocket
3. Frames de vídeo/áudio enviados via WebSocket
4. Chamada continua funcionando
5. Sistema tenta recuperar WebRTC em background
6. Quando WebRTC recupera, volta automaticamente
```

## 📊 Monitoramento e Detecção

### Health Check do WebRTC

- **Frequência**: A cada 3 segundos
- **Verificações**:
  - Estado da conexão (`connectionState`)
  - Estado ICE (`iceConnectionState`)
  - Stream remoto ativo
  - Qualidade da conexão

### Critérios de Fallback

O sistema alterna para WebSocket quando:
- `connectionState === 'failed'` ou `'disconnected'`
- `iceConnectionState === 'failed'`
- Stream remoto não está ativo
- 3 falhas consecutivas detectadas

### Recuperação Automática

- Sistema tenta voltar para WebRTC a cada 5 segundos
- Verifica se conexão está estável por 5 segundos
- Retorna automaticamente quando WebRTC recupera
- Transição suave sem interrupção da chamada

## 🎥 Transmissão de Mídia via WebSocket

### Vídeo

- **Resolução**: 640x480 (otimizado)
- **FPS**: 10 FPS (reduz carga)
- **Formato**: JPEG base64 (qualidade 50%)
- **Latência**: ~100-200ms adicional

### Áudio

- **Amostragem**: 2048 samples
- **Frequência**: 10 vezes por segundo
- **Formato**: Array de bytes
- **Nota**: Áudio via WebSocket é mais limitado que WebRTC

## ⚙️ Configuração

### Parâmetros Ajustáveis

```javascript
// Número de falhas antes de alternar para WebSocket
this.maxFailuresBeforeFallback = 3;

// Intervalo de health check (ms)
setInterval(..., 3000);

// FPS de captura WebSocket
setInterval(..., 100); // 10 FPS

// Qualidade JPEG
toDataURL('image/jpeg', 0.5); // 50%
```

## 📈 Vantagens do Sistema Híbrido

### Performance
- ✅ WebRTC quando possível (melhor qualidade)
- ✅ WebSocket quando necessário (garante continuidade)
- ✅ Alternância automática e transparente

### Confiabilidade
- ✅ Chamada nunca cai completamente
- ✅ Recuperação automática
- ✅ Funciona em qualquer tipo de rede

### Eficiência
- ✅ WebSocket só usado quando necessário
- ✅ Economia de banda quando WebRTC funciona
- ✅ Qualidade adaptativa

## 🔍 Indicadores Visuais

O sistema mostra indicador visual do modo atual:
- **Verde "WebRTC"**: Modo principal ativo
- **Amarelo "WebSocket (Fallback)"**: Modo fallback ativo (pisca)

## 🐛 Troubleshooting

### WebRTC sempre falha e vai para WebSocket
- Verifique firewall/NAT
- Adicione servidores TURN
- Verifique logs do console

### WebSocket não funciona
- Verifique se Reverb está rodando
- Verifique conexão WebSocket
- Verifique logs do servidor

### Alternância muito frequente
- Ajuste `maxFailuresBeforeFallback`
- Aumente intervalo de health check
- Verifique qualidade da rede

## 📝 Logs e Debug

O sistema loga todas as operações:
- `[WebRTC]` - Operações WebRTC
- `[WebSocket]` - Operações WebSocket
- `[Call]` - Operações de chamada

### Exemplo de Logs

```
[WebRTC] Iniciando chamada...
[WebRTC] Offer enviado
[WebSocket] Captura preparada
[WebRTC] Stream remoto recebido
[WebRTC] Health check falhou (1/3)
[WebRTC] Health check falhou (2/3)
[WebRTC] Health check falhou (3/3)
[WebRTC] Alternando para WebSocket (fallback)
[WebSocket] Transmissão de mídia iniciada
[Call] Modo de transmissão: websocket
[WebRTC] Tentando recuperar conexão WebRTC...
[WebRTC] WebRTC recuperado, voltando para modo principal
[Call] Modo de transmissão: webrtc
```

## 🚀 Próximas Melhorias

1. **Adaptive Bitrate**: Ajustar qualidade baseado na rede
2. **Áudio via WebSocket melhorado**: Usar codec de áudio
3. **Métricas de qualidade**: Mostrar latência, perda de pacotes
4. **Gravação híbrida**: Gravar ambos os streams
5. **Compartilhamento de tela**: Via WebSocket como fallback

