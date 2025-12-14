# ✅ Checklist - Configuração do Servidor Reverb

## Status da Configuração

### ✅ Branch Atual
- **Branch**: `reverb` ✓
- **Status**: Configurada corretamente

### ✅ Arquivo `.nixpacks.toml`
- **PHP**: 8.3 ✓
- **Composer**: Instalado ✓
- **Node/Nginx**: Removidos (não necessário) ✓
- **Extensões PHP**: Mínimas necessárias ✓
  - bcmath, ctype, curl, iconv, mbstring, opcache
  - pdo, pdo_mysql (para autenticação Laravel)
  - redis (para scaling)
  - zip
- **Comando de Start**: `php artisan reverb:start` ✓

### ✅ Configuração do Reverb

**Arquivo**: `config/reverb.php`
- Configurado para usar variáveis de ambiente ✓
- Porta padrão: 8080 ✓
- Host: 0.0.0.0 (aceita conexões de qualquer IP) ✓
- Suporte a Redis para scaling ✓

### ⚙️ Variáveis de Ambiente Necessárias

Para o servidor Reverb funcionar, você precisa configurar no Coolify/Dockploy:

```env
# Obrigatórias
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
REVERB_APP_ID=seu-app-id
REVERB_APP_KEY=sua-chave-publica
REVERB_APP_SECRET=sua-chave-secreta
REVERB_HOST=reverb.seudominio.com
REVERB_PORT=8080
REVERB_SCHEME=https

# Opcionais mas recomendadas
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_MAX_REQUEST_SIZE=10000

# Se usar Redis para scaling
REVERB_SCALING_ENABLED=false
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

### 🚀 Processo de Deploy

1. **No Coolify/Dockploy:**
   - Crie nova aplicação
   - Selecione branch: `reverb`
   - Build pack: Nixpacks (PHP)
   - Configure as variáveis de ambiente acima
   - Porta do container: `8080`

2. **Após o deploy:**
   - O servidor iniciará automaticamente com `php artisan reverb:start`
   - Verifique os logs para confirmar: "Reverb server started on 0.0.0.0:8080"

3. **Na aplicação principal:**
   - Use as mesmas credenciais (`REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`)
   - Configure `REVERB_HOST` com o endereço do servidor Reverb
   - Configure `BROADCAST_CONNECTION=reverb`

### ✅ Tudo Pronto!

A branch está configurada para iniciar o servidor Reverb automaticamente quando o container iniciar.

