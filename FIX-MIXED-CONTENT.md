# 🔒 Como Corrigir Mixed Content (HTTP em HTTPS)

## Problema

Erros como:
```
Mixed Content: The page at 'https://uniontechnicals.com/admin/login' was loaded over HTTPS, 
but requested an insecure stylesheet 'http://uniontechnicals.com/fonts/filament/...'
```

Isso acontece quando assets (CSS, JS, fontes) são carregados via HTTP em páginas HTTPS.

## ✅ Solução Aplicada

### 1. Middleware TrustProxies
Criado `app/Http/Middleware/TrustProxies.php` para reconhecer corretamente proxies (Nginx, Cloudflare).

### 2. AppServiceProvider
Configurado para forçar HTTPS quando:
- Ambiente é produção
- Está atrás de proxy com `X-Forwarded-Proto: https`
- `APP_URL` está configurado como HTTPS

### 3. Bootstrap/app.php
Adicionado `trustProxies()` para confiar em todos os proxies.

## 🚀 Passos para Aplicar (CRÍTICO)

### No Servidor de Produção:

1. **Configure APP_URL no .env:**
   ```env
   APP_URL=https://uniontechnicals.com
   ```

2. **Limpe TODOS os caches:**
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   php artisan cache:clear
   ```

3. **Recompile o cache (se necessário):**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Reinicie o servidor PHP-FPM:**
   ```bash
   # Dependendo do seu servidor:
   sudo systemctl restart php-fpm
   # Ou
   sudo service php-fpm restart
   ```

### Se estiver usando Coolify/Dockploy:

1. Configure a variável de ambiente `APP_URL`:
   ```
   APP_URL=https://uniontechnicals.com
   ```

2. Faça um novo deploy ou reinicie o container

3. Dentro do container, execute:
   ```bash
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

## 🔍 Verificação

Após aplicar as mudanças:

1. **Limpe o cache do navegador** (Ctrl+Shift+R ou Ctrl+F5)

2. **Verifique no DevTools (F12):**
   - Abra a aba "Network"
   - Recarregue a página
   - Verifique se TODOS os assets estão usando HTTPS
   - Procure por erros de Mixed Content

3. **Verifique no código fonte:**
   - Clique direito > "Ver código-fonte"
   - Procure por tags `<link>` e `<script>`
   - Todos devem começar com `https://`

## ⚠️ Problemas Comuns

### Cache ainda mostra HTTP

**Solução:** 
```bash
# Limpe tudo novamente
php artisan optimize:clear
# Ou individualmente:
php artisan config:clear && php artisan view:clear && php artisan route:clear
```

### Assets ainda em HTTP após limpar cache

**Solução:**
1. Verifique se `APP_URL` está correto no `.env`
2. Verifique se o ambiente é `production` (`APP_ENV=production`)
3. Reinicie o PHP-FPM/Nginx

### Funciona local mas não em produção

**Solução:**
1. Verifique se `APP_ENV=production` no servidor
2. Verifique se `APP_URL` está com HTTPS
3. Limpe o cache novamente no servidor

## 📝 Notas Técnicas

- O `URL::forceScheme('https')` força TODAS as URLs geradas pelo Laravel a usarem HTTPS
- Isso inclui: `asset()`, `route()`, `url()`, `secure_url()`, etc.
- O Filament usa `asset()` internamente, então será afetado automaticamente
- O cache de views pode armazenar URLs antigas - sempre limpe após mudanças

## ✅ Checklist Final

- [ ] `APP_URL=https://uniontechnicals.com` no .env
- [ ] `APP_ENV=production` no .env
- [ ] Cache limpo (`php artisan optimize:clear`)
- [ ] Servidor reiniciado
- [ ] Cache do navegador limpo
- [ ] Assets carregando via HTTPS (verificar no DevTools)

