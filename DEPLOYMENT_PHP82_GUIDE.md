# SiteLedger Deployment Guide for PHP 8.2 Servers

## Quick Fix for Your Current Issue

You're experiencing PHP version compatibility issues. Here's how to resolve them:

### Option 1: Use the Automated Script (Recommended)

1. **Run the PHP 8.2 compatibility script:**
   ```bash
   ./composer-php82-fix.sh
   ```

2. **If that doesn't work, manually fix dependencies:**
   ```bash
   rm composer.lock
   cp composer-php82.json composer.json
   composer update --with-dependencies
   ```

### Option 2: Manual Fix

1. **Remove problematic packages:**
   ```bash
   composer remove --dev pestphp/pest pestphp/pest-plugin-laravel --no-update
   ```

2. **Add compatible versions:**
   ```bash
   composer require --dev "pestphp/pest:^3.3" --no-update
   composer require --dev "pestphp/pest-plugin-laravel:^3.0" --no-update
   composer require --dev "phpunit/phpunit:^11.0" --no-update
   ```

3. **Update dependencies:**
   ```bash
   composer update --with-dependencies
   ```

## Complete Deployment Process

### Step 1: Pre-deployment Setup
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Remove lock file for fresh resolution
rm composer.lock
```

### Step 2: Fix Dependencies
```bash
# Use the automated script
./composer-php82-fix.sh

# OR manually copy the PHP 8.2 compatible composer.json
cp composer-php82.json composer.json
composer update
```

### Step 3: Application Setup
```bash
# Generate app key if needed
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Create storage link
php artisan storage:link
```

### Step 4: Production Optimization
```bash
# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 5: Frontend Assets (if Node.js available)
```bash
npm install --production
npm run build
```

### Step 6: Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public
```

## Environment Configuration

Make sure your `.env` file has the correct database settings:

```env
APP_NAME=SiteLedger
APP_ENV=production
APP_KEY=your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

## Web Server Configuration

### Apache (.htaccess should work automatically)
Point document root to `public/` directory.

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/siteledger/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Troubleshooting

### If composer update still fails:
1. Check PHP version: `php -v`
2. Update composer: `composer self-update`
3. Clear composer cache: `composer clear-cache`
4. Try: `composer update --ignore-platform-req=php`

### If migrations fail:
1. Check database connection: `php artisan tinker` → `DB::connection()->getPdo()`
2. Check database exists and user has permissions
3. Run migrations step by step: `php artisan migrate:status`

### If assets don't load:
1. Check public directory permissions
2. Run: `php artisan storage:link`
3. Verify web server points to `public/` directory

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS with SSL certificate
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Disable unnecessary PHP functions
- [ ] Keep Laravel and dependencies updated
- [ ] Use strong database passwords
- [ ] Enable Laravel's CSRF protection
- [ ] Configure rate limiting

## Quick Commands Reference

```bash
# Deployment automation
./deploy-php82.sh

# Composer fix for PHP 8.2
./composer-php82-fix.sh

# Clear all caches
php artisan optimize:clear

# Rebuild all caches
php artisan optimize

# Check application status
php artisan about
```

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Run: `php artisan about` for system information
4. Verify all requirements are met