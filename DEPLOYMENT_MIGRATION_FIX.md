# 🚀 SiteLedger Deployment Guide for Production Server

## Migration Conflict Resolution

The deployment is failing because some database tables already exist. Follow these steps to resolve:

### Option 1: Quick Fix (Recommended)

1. **Upload and run the deployment fix script:**
   ```bash
   # Upload deploy-fix.sh to your server
   chmod +x deploy-fix.sh
   ./deploy-fix.sh
   ```

### Option 2: Manual Fix

1. **Mark existing migrations as completed:**
   ```bash
   php artisan tinker
   ```
   
   Then run these commands in tinker:
   ```php
   // Check if tables exist and mark migrations
   $migrations = [
       '2025_10_31_124000_create_orders_table',
       '2025_10_31_124100_create_order_items_table', 
       '2025_10_31_150000_create_products_table',
       '2025_11_03_120000_create_worker_payments_table'
   ];
   
   foreach ($migrations as $migration) {
       if (!DB::table('migrations')->where('migration', $migration)->exists()) {
           DB::table('migrations')->insert([
               'migration' => $migration,
               'batch' => 1
           ]);
           echo "Marked {$migration} as completed\n";
       }
   }
   
   exit
   ```

2. **Run remaining migrations:**
   ```bash
   php artisan migrate --force
   ```

### Option 3: Fresh Database (if acceptable)

If you can reset the database:
```bash
php artisan migrate:fresh --force
php artisan db:seed --force
```

## After Migration Fix

1. **Clear and cache everything:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Optimize autoloader:**
   ```bash
   composer dump-autoload --optimize
   ```

## Environment Setup

Make sure your `.env` file has these settings for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database settings
DB_CONNECTION=mysql
DB_HOST=your-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

## File Permissions

Set proper permissions:
```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache
```

## Testing Deployment

1. **Check if the application loads:**
   ```bash
   curl -I https://your-domain.com
   ```

2. **Test database connection:**
   ```bash
   php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';"
   ```

3. **Verify migrations:**
   ```bash
   php artisan migrate:status
   ```

## Troubleshooting

### If migrations still fail:
1. Check database permissions
2. Verify database credentials in `.env`
3. Ensure MySQL/MariaDB is running
4. Check Laravel logs: `tail -f storage/logs/laravel.log`

### If application doesn't load:
1. Check web server configuration
2. Verify document root points to `/public`
3. Check `.htaccess` file exists in public folder
4. Verify PHP version (8.2+ required)

## Production Optimization

Once deployed successfully:
```bash
# Enable OPcache
php artisan optimize

# Queue workers (if using queues)
php artisan queue:work --daemon

# Schedule cron job for task scheduling
# Add to crontab: * * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong APP_KEY generated
- [ ] Database credentials secured
- [ ] File permissions set correctly
- [ ] SSL certificate installed
- [ ] Firewall configured
- [ ] Regular backups scheduled