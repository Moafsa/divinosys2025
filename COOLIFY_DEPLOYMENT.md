# Deployment Guide for Coolify

## Sistema Divino Lanches 2.0

### Pre-deployment Checklist

✅ **Case Sensitivity Fixed**: All file references have been converted from uppercase to lowercase to ensure Linux compatibility.

### Key Changes Made

1. **Fixed File References**: 
   - `MVC/MODEL/` → `mvc/model/`
   - `MVC/VIEWS/` → `mvc/views/`
   - `MVC/CONTROLLER/` → `mvc/controller/`
   - `MVC/CONFIG/` → `mvc/config/`
   - `MVC/COMMON/` → `mvc/common/`
   - `MVC/UPLOADS/` → `mvc/uploads/`

2. **Updated Include/Require Statements**: All PHP include and require statements now use lowercase paths.

3. **CSS/JS Asset References**: All asset links in header.php and other files now use lowercase paths.

### Coolify Deployment Steps

1. **Database Setup**:
   - Create a MySQL 8.0 database service in Coolify
   - Import the database schema from `divinosys.sql` or `pdv_db.sql`
   - Note the database credentials for environment variables

2. **Environment Variables**:
   Set the following environment variables in Coolify:
   ```
   DB_HOST=your-database-host
   DB_PORT=3306
   DB_NAME=divinosys
   DB_USER=your-db-user
   DB_PASS=your-db-password
   DB_CHARSET=utf8mb4
   DB_COLLATION=utf8mb4_unicode_ci
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   TZ=America/Sao_Paulo
   ```

3. **Docker Configuration**:
   - The project includes a `Dockerfile` optimized for PHP 8.2 with Apache
   - All necessary PHP extensions are included (mysqli, gd, zip, etc.)
   - Apache rewrite module is enabled
   - Timezone is set to America/Sao_Paulo

4. **File Permissions**:
   - Ensure the `mvc/uploads/` directory is writable
   - Ensure the `uploads/` directory is writable
   - Log directories should be writable

5. **Post-deployment Tasks**:
   - Test database connectivity through the application
   - Verify file uploads work correctly
   - Check that all assets load properly (CSS/JS)
   - Test the login functionality
   - Verify POS operations work correctly

### Docker Build Command
```bash
docker build -t divinosys .
```

### Important Notes

- **Case Sensitivity**: Linux is case-sensitive, so all file references must be lowercase
- **File Uploads**: Ensure proper permissions for upload directories
- **Database**: Make sure to import the latest database schema
- **Assets**: All CSS/JS assets are included in the project

### Troubleshooting

If you encounter file not found errors:
1. Check that all file references are lowercase
2. Verify file permissions
3. Check database connectivity
4. Review error logs in `mvc/logs/` and `logs/`

### Performance Considerations

- The system is optimized for restaurant POS operations
- Database queries are prepared statements for security
- Static assets are served directly by Apache
- Session management is properly configured

### Security Notes

- All database queries use prepared statements
- Session security is configured for production
- File upload validation is implemented
- CSRF protection should be enabled in production
