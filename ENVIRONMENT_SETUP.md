# Environment Configuration Guide

## Overview
This system is now 100% configurable via environment variables, making it portable across any domain or server.

## Required Environment Variables

### Database Configuration
```env
DB_HOST=db
DB_PORT=3306
DB_NAME=divinosys
DB_USER=your_db_user_here
DB_PASS=your_secure_password_here
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### Application Configuration
```env
APP_URL=https://your-domain.com
APP_PORT=80
APP_ENV=production
APP_DEBUG=false
APP_NAME=DivinoSys
APP_VERSION=2.0
```

### Protocol Configuration
```env
# Set to 'https' for production, 'http' for development
APP_PROTOCOL=https
# Set to 'true' to force HTTPS, 'false' to use HTTP
FORCE_HTTPS=true
# Set to 'true' for production domains, 'false' for development
IS_PRODUCTION=true
```

### Security Configuration
```env
SESSION_SECURE=true
SESSION_HTTPONLY=true
SESSION_LIFETIME=120
```

### File Upload Configuration
```env
UPLOAD_MAX_SIZE=64M
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,gif,pdf,doc,docx
```

### Timezone Configuration
```env
TZ=America/Sao_Paulo
```

## Configuration Examples

### Local Development
```env
APP_URL=http://localhost:8081
APP_PORT=8081
APP_ENV=development
APP_PROTOCOL=http
FORCE_HTTPS=false
IS_PRODUCTION=false
SESSION_SECURE=false
```

### Production (Coolify)
```env
APP_URL=https://divinosys.conext.click
APP_PORT=80
APP_ENV=production
APP_PROTOCOL=https
FORCE_HTTPS=true
IS_PRODUCTION=true
SESSION_SECURE=true
```

### Custom Domain
```env
APP_URL=https://meusite.com
APP_PORT=80
APP_ENV=production
APP_PROTOCOL=https
FORCE_HTTPS=true
IS_PRODUCTION=true
SESSION_SECURE=true
```

## How to Deploy

### 1. Local Development
1. Copy `coolify.env.example` to `.env`
2. Update the variables for local development
3. Run `docker-compose up -d`

### 2. Production (Coolify)
1. Set the environment variables in Coolify dashboard
2. Deploy the application
3. The system will automatically use the correct protocol and settings

### 3. Custom Server
1. Set the environment variables in your server
2. Update the database credentials
3. Deploy the application
4. The system will automatically configure itself

## Testing
Access `/test_env_config.php` to verify your environment configuration.

## Benefits
- ✅ **Portable**: Works on any domain or server
- ✅ **Configurable**: All settings via environment variables
- ✅ **Secure**: No hardcoded credentials or URLs
- ✅ **Flexible**: Easy to switch between environments
- ✅ **Maintainable**: Single source of truth for configuration
