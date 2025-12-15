# 🔧 Configuração Nixpacks - Build Automático

## ✅ Problema Resolvido

**Antes**: Era necessário instalar Node.js manualmente e executar build após cada deploy.

**Agora**: O build é executado automaticamente durante o deploy pelo Nixpacks.

## 🔄 Mudanças Aplicadas

### 1. **`.nixpacks.toml`**

#### Fase de Instalação (`[phases.install]`)
- ✅ Verificação de Node.js e NPM antes de instalar
- ✅ Instalação de **todas as dependências** (incluindo devDependencies)
  - **Mudança crítica**: Removido `--omit=dev` porque Vite, Tailwind e outras ferramentas de build são devDependencies
- ✅ Uso de `npm ci` quando `package-lock.json` existe (mais rápido e confiável)
- ✅ Fallback para `npm install` se não houver lock file

#### Fase de Build (`[phases.build]`)
- ✅ Build executado **ANTES** do cache do Laravel
- ✅ Verificação de versão do Node antes do build
- ✅ Fallback automático se build falhar (reinstala e tenta novamente)
- ✅ Verificação se o diretório `public/build/` foi gerado
- ✅ Logs detalhados para debug

#### Variáveis de Ambiente
- ✅ `NIXPACKS_NODE_VERSION = "18"` - Força uso do Node 18
- ✅ `NODE_ENV = "production"` - Otimiza build para produção

### 2. **`package.json`**
- ✅ Campo `engines` especificando Node 18.x
- ✅ Script `postinstall` para verificar versões

### 3. **`.nvmrc`**
- ✅ Arquivo criado com versão `18` para detecção automática

## 📋 Fluxo de Deploy Automático

```
1. Setup
   ├── Instala PHP 8.3
   ├── Instala Composer
   ├── Instala Node.js 18 ✅
   └── Instala Nginx

2. Install
   ├── Instala dependências Composer
   ├── Verifica Node.js e NPM ✅
   ├── Instala dependências NPM (incluindo dev) ✅
   └── Confirma instalação

3. Build
   ├── Verifica versões Node/NPM ✅
   ├── Executa npm run build ✅
   ├── Verifica se build foi gerado ✅
   ├── Gera cache do Laravel
   └── Confirma conclusão

4. Start
   └── Inicia Nginx servindo a aplicação
```

## 🔍 Verificações Adicionadas

### Durante o Build
- ✅ Verificação de Node.js disponível
- ✅ Verificação de NPM disponível
- ✅ Verificação se `public/build/` foi criado
- ✅ Logs detalhados em cada etapa

### Fallback Automático
Se o build falhar:
1. Tenta reinstalar dependências
2. Executa build novamente
3. Se ainda falhar, mostra erro detalhado

## ⚠️ Importante

### Dependências de Build
As seguintes dependências são **necessárias** para o build e estão em `devDependencies`:
- `vite` - Build tool
- `@tailwindcss/vite` - Tailwind CSS
- `laravel-vite-plugin` - Plugin Laravel
- `tailwindcss` - Framework CSS

Por isso, **NÃO** usar `--omit=dev` na instalação!

### package-lock.json
- ✅ O arquivo `package-lock.json` deve estar commitado no repositório
- ✅ Isso garante versões consistentes entre deploys

## 🚀 Próximo Deploy

No próximo deploy, o Nixpacks irá:

1. ✅ Detectar Node.js 18 automaticamente
2. ✅ Instalar todas as dependências (incluindo dev)
3. ✅ Executar `npm run build` automaticamente
4. ✅ Gerar assets em `public/build/`
5. ✅ Iniciar a aplicação com assets prontos

**Você não precisará mais fazer build manual!**

## 🐛 Troubleshooting

### Se o build ainda falhar:

1. **Verificar logs do deploy**:
   - Procure por mensagens `===` nos logs
   - Verifique se Node.js 18 está sendo usado

2. **Verificar package-lock.json**:
   ```bash
   git status package-lock.json
   ```
   - Deve estar commitado

3. **Verificar versão do Node**:
   - Logs devem mostrar `v18.x.x`

4. **Verificar se public/build existe**:
   - Após deploy, verifique se `public/build/` foi criado

## 📝 Checklist de Deploy

- [x] Node.js 18 configurado no `.nixpacks.toml`
- [x] `engines` especificado no `package.json`
- [x] `.nvmrc` criado com versão 18
- [x] `package-lock.json` commitado
- [x] Build executado na fase correta
- [x] DevDependencies incluídas na instalação
- [x] Variáveis de ambiente configuradas

---

**Data**: {{ date('Y-m-d') }}  
**Status**: ✅ Configurado e pronto para deploy automático

