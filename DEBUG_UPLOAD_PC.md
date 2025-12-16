# 🐛 Debug: Upload Funciona no Celular mas Não no PC

## ❌ Problema

- **Celular**: Upload funciona ✅
- **PC**: Preview aparece, mas ao enviar dá erro ❌

---

## ✅ Melhorias Aplicadas

### 1. **Logging Detalhado no Frontend**

Agora o console mostra exatamente o que está acontecendo:

```javascript
[Chat] 📎 Arquivo anexado: {name: "foto.jpg", size: 123456, type: "image/jpeg"}
[Chat] 📤 Enviando mensagem... {hasMessage: false, hasFile: true}
[Chat] 📥 Resposta recebida: {status: 200, statusText: "OK", ok: true}
[Chat] ✅ Mensagem enviada com sucesso
```

**OU se der erro:**
```javascript
[Chat] ❌ Erro ao enviar mensagem: Tipo de arquivo não permitido
```

### 2. **Logging Detalhado no Backend**

O servidor agora loga:

```
[info] Upload de arquivo no chat: {room_code: "ABC", original_name: "foto.jpg", mime_type: "image/jpeg", size: 123456}
[info] Arquivo salvo com sucesso: {file_path: "chat_files/ABC/123_foto.jpg"}
```

### 3. **Mais Tipos de Arquivo Permitidos**

**Antes:** Apenas JPG, PNG, GIF, WEBP, PDF, DOC

**Agora:**
- ✅ Imagens: JPG, JPEG, PNG, GIF, WEBP, BMP
- ✅ Documentos: PDF, DOC, DOCX, XLS, XLSX, TXT
- ✅ Compactados: ZIP, RAR

### 4. **Mensagens de Erro Mais Claras**

- ✅ "Erro de conexão. Verifique sua internet."
- ✅ "Erro de segurança. Recarregue a página (F5)."
- ✅ "Arquivo muito grande. Máximo 10MB."
- ✅ "Tipo de arquivo não permitido: image/heic" (mostra o tipo exato)

---

## 🧪 Como Testar Agora

### Passo 1: Abrir Console do Navegador

**No PC:**
1. Pressione **F12** (Chrome/Edge) ou **Ctrl+Shift+I** (Firefox)
2. Vá na aba **Console**
3. Deixe aberto

### Passo 2: Tentar Enviar Arquivo

1. Clique no **📎 Anexo**
2. Selecione uma **imagem**
3. Veja o **preview aparecer**
4. Clique em **Enviar**

### Passo 3: Copiar os Logs

**Se der erro**, copie TODOS os logs do console que começam com `[Chat]`:

```
[Chat] 📎 Arquivo anexado: ...
[Chat] 📤 Enviando mensagem...
[Chat] ❌ Erro ao enviar mensagem: ...
```

**Me envie esses logs!**

---

## 🔍 Possíveis Causas e Soluções

### Causa 1: CSRF Token Expirado/Inválido

**Sintoma:**
```
[Chat] ⚠️ CSRF token não encontrado!
OU
[Chat] ❌ Erro: 419 CSRF token mismatch
```

**Solução:**
```
Recarregar a página (F5 ou Ctrl+F5)
```

---

### Causa 2: Tipo MIME Diferente no PC

**Sintoma:**
```
[Chat] 📎 Arquivo anexado: {type: "image/heic"}
[Chat] ❌ Tipo de arquivo não permitido: image/heic
```

**Explicação:** Alguns PCs/iPhones usam formatos diferentes (HEIC, TIFF, etc).

**Solução:** Converter imagem para JPG ou PNG antes de enviar.

**OU** me avise o tipo que apareceu no log para eu adicionar!

---

### Causa 3: Arquivo Muito Grande

**Sintoma:**
```
[Chat] 📎 Arquivo anexado: {size: 15728640} <- 15MB!
[Chat] ❌ Arquivo muito grande. Máximo 10MB.
```

**Solução:** Comprimir ou reduzir tamanho da imagem.

---

### Causa 4: Problema de Permissão no Storage

**Sintoma (backend log):**
```
[error] Erro ao salvar arquivo: Permission denied
```

**Solução (no servidor):**
```bash
chmod -R 775 storage
chown -R www-data:www-data storage
```

---

### Causa 5: Erro de Rede/CORS

**Sintoma:**
```
[Chat] ❌ Erro ao enviar mensagem: Failed to fetch
```

**Solução:**
1. Verificar conexão com internet
2. Verificar se CORS está configurado no servidor
3. Verificar se firewall não está bloqueando

---

## 📊 Comparação: Celular vs PC

| Aspecto | Celular | PC | Possível Problema |
|---------|---------|----|--------------------|
| **Formato de imagem** | JPEG | HEIC/TIFF? | Tipo MIME diferente |
| **Tamanho** | 2MB | 15MB? | Arquivo muito grande |
| **Navegador** | Chrome Mobile | ? | Comportamento diferente |
| **CSRF Token** | OK | Expirado? | Sessão antiga no PC |

---

## 🎯 Ação Imediata

**Faça isso agora:**

1. **Abra o console (F12)**
2. **Tente enviar uma imagem do PC**
3. **Copie TODOS os logs** que aparecem
4. **Me envie aqui**

**Exemplo do que preciso ver:**
```
[Chat] 📎 Arquivo anexado: {name: "imagem.jpg", size: 5242880, type: "image/jpeg"}
[Chat] 📤 Enviando mensagem... {hasMessage: false, hasFile: true, messageLength: 0}
[Chat] 📥 Resposta recebida: {status: 422, statusText: "Unprocessable Entity", ok: false}
[Chat] ❌ Erro na resposta: {success: false, message: "Tipo de arquivo não permitido: image/heic"}
[Chat] ❌ Erro ao enviar mensagem: Tipo de arquivo não permitido: image/heic
```

Com esses logs, vou saber **exatamente** qual é o problema! 🔍

---

## 💡 Teste Rápido

Tente enviar:

1. **Uma imagem JPG pequena** (menos de 1MB)
2. **Do mesmo PC que está dando problema**
3. **Com console aberto**

Se funcionar = Problema é tamanho/tipo de arquivo
Se não funcionar = Problema é outra coisa (vou ver nos logs)

---

**Me mostre os logs do console que eu resolvo! 🚀**
