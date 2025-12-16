# 🔧 Correção: Imagens Não Aparecem (404)

## ❌ Problema

```
GET https://uniontechnicals.com/storage/chat_files/.../image.png 404 (Not Found)
```

A imagem foi enviada com sucesso, mas não está aparecendo no chat.

---

## ✅ Solução Aplicada

### 1. **Código Atualizado para Usar `Storage::url()`**

**Antes:**
```blade
<img src="{{ asset('storage/' . $message->file_path) }}">
```

**Agora:**
```blade
@php
    $fileUrl = \Storage::disk('public')->url($message->file_path);
@endphp
<img src="{{ $fileUrl }}">
```

**Benefício:** `Storage::url()` é mais confiável e respeita a configuração do `.env`.

---

## 🚀 Comandos para Executar no Servidor

### Passo 1: Verificar se Link Simbólico Existe

```bash
# SSH no servidor
ssh usuario@uniontechnicals.com

# Ir para a pasta do projeto
cd /caminho/do/projeto

# Verificar se link existe
ls -la public/storage
```

**Resultado esperado:**
```
public/storage -> /caminho/completo/storage/app/public
```

Se **NÃO existir**, vá para o Passo 2.

---

### Passo 2: Criar Link Simbólico

```bash
php artisan storage:link
```

**Se der erro "link already exists":**
```bash
# Remover link antigo
rm public/storage

# Criar novo
php artisan storage:link
```

---

### Passo 3: Verificar Permissões

```bash
# Dar permissão para storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Dar permissão para o link
chmod -R 755 public/storage

# Garantir que o servidor web é dono
chown -R www-data:www-data storage
chown -R www-data:www-data public/storage
```

*Nota:* Substitua `www-data` pelo usuário do seu servidor web (pode ser `nginx`, `apache`, etc).

---

### Passo 4: Verificar se Arquivo Existe

```bash
# Ver se arquivo existe fisicamente
ls -lh storage/app/public/chat_files/DsDwwz3bzIe5pWShoC9G/

# Deve listar os arquivos
# Ex: 1765914076_IMG_5713.png
```

Se o arquivo **NÃO existir** no caminho acima, o upload não funcionou corretamente.

---

## 🔍 Diagnóstico Completo

Execute este script no servidor para diagnóstico:

```bash
#!/bin/bash
echo "=== DIAGNÓSTICO DE STORAGE ==="
echo ""

echo "1. Link simbólico existe?"
if [ -L "public/storage" ]; then
    echo "  ✅ SIM"
    echo "  └─ Aponta para: $(readlink -f public/storage)"
else
    echo "  ❌ NÃO! Execute: php artisan storage:link"
fi

echo ""
echo "2. Diretório storage/app/public existe?"
if [ -d "storage/app/public" ]; then
    echo "  ✅ SIM"
else
    echo "  ❌ NÃO! Crie: mkdir -p storage/app/public"
fi

echo ""
echo "3. Permissões do storage:"
ls -ld storage/app/public

echo ""
echo "4. Arquivos em chat_files:"
if [ -d "storage/app/public/chat_files" ]; then
    du -sh storage/app/public/chat_files/*
else
    echo "  ⚠️ Pasta chat_files não existe ainda"
fi

echo ""
echo "5. URL configurada no .env:"
grep "APP_URL" .env

echo ""
echo "=== FIM DO DIAGNÓSTICO ==="
```

**Salve como `diagnose-storage.sh` e execute:**
```bash
chmod +x diagnose-storage.sh
./diagnose-storage.sh
```

---

## ⚙️ Verificar Configuração do .env

Edite o arquivo `.env` no servidor:

```bash
nano .env
```

Certifique-se que tem:

```env
APP_URL=https://uniontechnicals.com

# NÃO deve ter barra no final:
# APP_URL=https://uniontechnicals.com/  ← ERRADO
```

Após editar:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🌐 Alternativa: Servir Arquivos Diretamente

Se o link simbólico não funcionar (alguns servidores bloqueiam), você pode servir arquivos via rota:

### Adicione em `routes/web.php`:

```php
use Illuminate\Support\Facades\Storage;

// Servir arquivos de chat
Route::get('/storage/chat_files/{roomCode}/{filename}', function ($roomCode, $filename) {
    $path = "chat_files/{$roomCode}/{$filename}";
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    
    return response()->file(
        Storage::disk('public')->path($path)
    );
})->where('filename', '.*');
```

**Vantagens:**
- Funciona mesmo sem link simbólico
- Controle de acesso (pode adicionar autenticação)

**Desvantagens:**
- Passa pelo PHP (um pouco mais lento que servir direto)

---

## 📊 Checklist de Solução

Execute em ordem:

- [ ] **SSH no servidor**
- [ ] **Ir para pasta do projeto**
- [ ] **Executar `php artisan storage:link`**
- [ ] **Verificar se link foi criado: `ls -la public/storage`**
- [ ] **Ajustar permissões: `chmod -R 775 storage`**
- [ ] **Verificar APP_URL no .env**
- [ ] **Limpar cache: `php artisan config:clear`**
- [ ] **Testar URL manualmente**:
  ```
  https://uniontechnicals.com/storage/chat_files/DsDwwz3bzIe5pWShoC9G/1765914076_IMG_5713.png
  ```
- [ ] **Se funcionar = ✅ Resolvido!**

---

## 🧪 Teste Rápido

Após executar os comandos, teste no navegador:

```
https://uniontechnicals.com/storage/
```

**Resultado esperado:**
- Se link está correto: Erro 403 ou 404 (normal, não tem index)
- Se link NÃO existe: Erro 404

Depois teste o arquivo específico:
```
https://uniontechnicals.com/storage/chat_files/DsDwwz3bzIe5pWShoC9G/1765914076_IMG_5713.png
```

**Deve exibir a imagem!**

---

## 💡 Dica

Para evitar problemas futuros, adicione ao seu script de deploy:

```bash
# deploy.sh
php artisan storage:link
chmod -R 775 storage
php artisan config:clear
php artisan cache:clear
```

---

**Execute os comandos no servidor e me avise o resultado!** 🚀
