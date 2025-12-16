# 📁 Configuração do Storage e Upload de Arquivos

## ✅ O Que Foi Implementado

### 1. **Filesystem Restaurado para Padrão Laravel**
O arquivo `config/filesystems.php` foi restaurado para a configuração original do Laravel:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],

'links' => [
    public_path('storage') => storage_path('app/public'),
],
```

### 2. **Upload de Arquivos no Chat**
- ✅ Botão de anexo com ícone de clipe
- ✅ Preview de imagem antes de enviar
- ✅ Preview de arquivo com nome e tamanho
- ✅ Suporte a drag-and-drop
- ✅ Validação de tamanho (máx 10MB)
- ✅ Backend já preparado

---

## 🚀 Passos para Ativar

### Passo 1: Criar Link Simbólico

Execute no terminal:

```bash
php artisan storage:link
```

**O que isso faz:**
- Cria um link simbólico de `public/storage` → `storage/app/public`
- Permite que arquivos em `storage/app/public` sejam acessíveis via URL

**Resultado esperado:**
```
The [public/storage] link has been connected to [storage/app/public].
```

---

### Passo 2: Criar Diretórios (se não existirem)

```bash
mkdir -p storage/app/public/chat_files
mkdir -p storage/app/public/services
mkdir -p storage/app/public/brands
```

---

### Passo 3: Mover Imagens Existentes (se houver)

#### Imagens de Serviços
Se você tem imagens em `public/images/services/`:
```bash
# Criar diretório
mkdir -p storage/app/public/services

# Mover arquivos (Windows PowerShell)
Move-Item public\images\services\* storage\app\public\services\
```

#### Imagens de Marcas
Se você tem imagens em `public/images/brands/`:
```bash
mkdir -p storage/app/public/brands
Move-Item public\images\brands\* storage\app\public\brands\
```

---

## 📂 Estrutura de Diretórios


Após configurar, a estrutura será:

```
storage/
└── app/
    └── public/          ← Arquivos públicos
        ├── chat_files/  ← Arquivos do chat
        │   └── {room_code}/
        │       ├── image1.jpg
        │       └── document1.pdf
        ├── services/    ← Imagens de serviços
        │   └── service1.jpg
        └── brands/      ← Imagens de marcas
            └── brand1.png

public/
└── storage/  ← Link simbólico → storage/app/public
```

---

## 🔧 Atualizar Referências no Código

### Filament (Imagens de Serviços/Marcas)

Se você estiver usando Filament para gerenciar serviços/marcas, atualize os FileUpload:

```php
// Em ServiceResource.php ou similar
FileUpload::make('photo_patch')
    ->label('Foto do Serviço')
    ->disk('public')  // Usar disco public
    ->directory('services')  // Salvar em services/
    ->image()
    ->maxSize(2048);

// Para Marcas
FileUpload::make('logo')
    ->label('Logo da Marca')
    ->disk('public')
    ->directory('brands')
    ->image()
    ->maxSize(1024);
```

### Exibir Imagens na View

**Antes (errado):**
```blade
<img src="{{ asset('images/services/'.$service->photo_patch) }}">
```

**Depois (correto):**
```blade
<img src="{{ asset('storage/services/'.$service->photo_patch) }}">
ou
<img src="{{ Storage::url('services/'.$service->photo_patch) }}">
```

---

## 🧪 Testar Upload de Arquivos

1. **Abra o chat**
2. **Clique no ícone de clipe** 📎
3. **Selecione uma imagem** (PNG, JPG, etc)
4. **Veja o preview** aparecer
5. **Digite uma mensagem** (opcional)
6. **Clique em Enviar** ✈️

**Preview de Imagem:**
- Mostra miniatura da imagem
- Botão X para remover

**Preview de Arquivo:**
- Mostra nome do arquivo
- Mostra tamanho (KB/MB)
- Botão X para remover

---

## 📝 Tipos de Arquivo Suportados

### Imagens
- JPG, JPEG
- PNG
- GIF
- WEBP

### Documentos
- PDF
- DOC, DOCX (Word)
- XLS, XLSX (Excel)
- TXT
- ZIP, RAR

**Tamanho máximo:** 10MB

---

## 🔍 Verificar se Está Funcionando

### 1. Link Simbólico Existe?
```bash
# Windows PowerShell
Test-Path public\storage
# Deve retornar: True

# Ver para onde aponta
Get-Item public\storage | Select-Object Target
# Deve mostrar: storage\app\public
```

### 2. Testar URL de Arquivo

Após criar o link, acesse:
```
http://seu-dominio.com/storage/chat_files/test.jpg
```

Se funcionar = ✅ Configuração correta!

---

## ⚠️ Problemas Comuns

### Erro: "Link simbólico já existe"
```bash
# Remover link antigo
Remove-Item public\storage

# Criar novo
php artisan storage:link
```

### Imagens não aparecem
**Causas:**
1. Link simbólico não criado → Execute `php artisan storage:link`
2. Arquivo não está em `storage/app/public` → Mova para lá
3. URL errada na view → Use `asset('storage/...')`

### Permissões (Linux/Mac)
```bash
chmod -R 775 storage
chmod -R 775 public/storage
```

---

## 🌐 Produção (Servidor)

Quando fazer deploy:

```bash
# 1. Fazer upload dos arquivos
# 2. Executar comandos
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

**IMPORTANTE:** O link simbólico precisa ser criado **no servidor de produção** também!

---

## 📊 Resumo das Mudanças

| Item | Antes | Depois |
|------|-------|--------|
| **Disco public** | `public/app/public` | `storage/app/public` ✅ |
| **Link simbólico** | `public/public` | `public/storage` ✅ |
| **URL de acesso** | `APP_URL` | `APP_URL/storage` ✅ |
| **Upload no chat** | ❌ Não tinha | ✅ Completo com preview |
| **Drag and drop** | ❌ Não | ✅ Sim |

---

## ✅ Checklist

Execute em ordem:

- [ ] `php artisan storage:link`
- [ ] Criar diretórios: `chat_files`, `services`, `brands`
- [ ] Mover imagens antigas (se houver)
- [ ] Atualizar FileUpload do Filament (disk='public')
- [ ] Testar upload de imagem no chat
- [ ] Testar upload de PDF no chat
- [ ] Verificar se imagens aparecem corretamente

---

**Pronto! Agora seu sistema está usando o padrão Laravel para arquivos.** 🎉

Execute `php artisan storage:link` e teste o upload no chat!
