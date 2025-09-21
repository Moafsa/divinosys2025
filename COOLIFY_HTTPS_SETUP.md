# Configuração HTTPS no Coolify

## Problema Resolvido

O sistema agora está configurado para funcionar com qualquer domínio usando apenas variáveis de ambiente, sem hardcoding de domínios específicos.

## Configuração no Coolify

### 1. Variáveis de Ambiente Obrigatórias

No painel do Coolify, configure estas variáveis:

```bash
# Database
DB_HOST=db
DB_PORT=3306
DB_NAME=divinosys
DB_USER=divino
DB_PASS=sua_senha_segura

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
APP_PROTOCOL=https
FORCE_HTTPS=true
IS_PRODUCTION=true

# Security
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_LIFETIME=120
```

### 2. Configuração de Domínio no Coolify

1. **No painel do Coolify:**
   - Vá para seu projeto
   - Clique em "Settings" ou "Configurações"
   - Em "Domain", configure seu domínio personalizado
   - **IMPORTANTE:** Ative SSL/HTTPS automaticamente

2. **O Coolify deve:**
   - Configurar automaticamente o certificado SSL
   - Redirecionar HTTP para HTTPS
   - Passar os headers corretos (`X-Forwarded-Proto: https`)

### 3. Como o Sistema Funciona Agora

1. **Prioridade das configurações:**
   - `APP_URL` - URL completa da aplicação
   - `APP_PROTOCOL` - Protocolo (http/https)
   - `FORCE_HTTPS=true` - Força HTTPS
   - `IS_PRODUCTION=true` - Ambiente de produção

2. **Detecção automática:**
   - Se `APP_URL` estiver definido, usa ela
   - Se `FORCE_HTTPS=true`, força HTTPS
   - Caso contrário, detecta automaticamente pelos headers

### 4. Para Desenvolvimento Local

```bash
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost
APP_PROTOCOL=http
FORCE_HTTPS=false
IS_PRODUCTION=false
```

### 5. Para Produção (Qualquer Domínio)

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://meudominio.com
APP_PROTOCOL=https
FORCE_HTTPS=true
IS_PRODUCTION=true
```

## Solução de Problemas

### Se ainda houver Mixed Content:

1. **Verifique as variáveis de ambiente no Coolify**
2. **Confirme que o SSL está ativo**
3. **Teste o script de debug:** `https://seu-dominio.com/debug_config.php`
4. **Verifique os logs do servidor**

### Logs de Debug

O sistema agora gera logs detalhados que mostram:
- Quais variáveis de ambiente estão definidas
- Como o protocolo está sendo detectado
- URLs finais geradas

## Vantagens da Nova Configuração

✅ **Funciona com qualquer domínio**
✅ **Configuração 100% via variáveis de ambiente**
✅ **Sem hardcoding de domínios**
✅ **Compatível com Coolify**
✅ **Funciona em desenvolvimento e produção**
✅ **Detecção automática de HTTPS**
