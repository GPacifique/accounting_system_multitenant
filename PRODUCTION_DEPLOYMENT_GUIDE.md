# 🚀 SiteLedger Production Deployment Guide

## Pre-Deployment Checklist

### 📋 **Required Server Specifications**
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: Minimum 512MB RAM (1GB+ recommended)
- **Storage**: Minimum 1GB free space

### 🔧 **Required PHP Extensions**
```bash
# Install required PHP extensions
sudo apt install php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd php8.2-bcmath php8.2-intl php8.2-redis
```

## 🚀 Production Deployment Steps

### 1. **Server Setup**

#### For Ubuntu/Debian:
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install web server (choose one)
# Option A: Apache
sudo apt install apache2
sudo a2enmod rewrite
sudo systemctl enable apache2

# Option B: Nginx
sudo apt install nginx
sudo systemctl enable nginx

# Install MySQL
sudo apt install mysql-server
sudo mysql_secure_installation

# Install Redis (recommended for production)
sudo apt install redis-server
sudo systemctl enable redis-server
```

### 2. **Deploy Application Code**

```bash
# Clone repository to production server
git clone https://github.com/GPacifique/siteledger.git /var/www/siteledger
cd /var/www/siteledger

# Set proper ownership
sudo chown -R www-data:www-data /var/www/siteledger
sudo chmod -R 755 /var/www/siteledger
sudo chmod -R 775 /var/www/siteledger/storage
sudo chmod -R 775 /var/www/siteledger/bootstrap/cache
```

### 3. **Install Dependencies**

```bash
# Install Composer if not already installed
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Install and build frontend assets
npm install
npm run build
```

### 4. **Environment Configuration**

```bash
# Copy production environment template
cp .env.production .env

# Edit environment file with your production values
nano .env

# Generate application key
php artisan key:generate

# IMPORTANT: Update these values in .env:
# - APP_ENV=production
# - APP_DEBUG=false
# - APP_URL=https://yourdomain.com
# - Database credentials
# - Mail server settings
# - Redis settings (if using)
```

### 5. **Database Setup**

```bash
# Create production database
mysql -u root -p
CREATE DATABASE siteledger_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'siteledger_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON siteledger_production.* TO 'siteledger_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# Seed essential data (roles, permissions, admin user)
php artisan db:seed --class=RolePermissionSeeder --force
php artisan db:seed --class=AdminUserSeeder --force
```

### 6. **Optimize for Production**

```bash
# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Create symbolic link for storage
php artisan storage:link
```

### 7. **Web Server Configuration**

#### Apache Configuration:
Create `/etc/apache2/sites-available/siteledger.conf`:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/siteledger/public
    
    <Directory /var/www/siteledger/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/siteledger_error.log
    CustomLog ${APACHE_LOG_DIR}/siteledger_access.log combined
</VirtualHost>
```

```bash
# Enable site and restart Apache
sudo a2ensite siteledger.conf
sudo systemctl restart apache2
```

#### Nginx Configuration:
Create `/etc/nginx/sites-available/siteledger`:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/siteledger/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

```bash
# Enable site and restart Nginx
sudo ln -s /etc/nginx/sites-available/siteledger /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 8. **SSL Certificate (Recommended)**

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx   # For Nginx

# Get SSL certificate
sudo certbot --apache -d yourdomain.com  # For Apache
# OR
sudo certbot --nginx -d yourdomain.com   # For Nginx
```

### 9. **Setup Cron Jobs**

```bash
# Add to crontab for www-data user
sudo crontab -u www-data -e

# Add this line:
* * * * * cd /var/www/siteledger && php artisan schedule:run >> /dev/null 2>&1
```

### 10. **Setup Queue Worker (Optional)**

```bash
# Create systemd service for queue worker
sudo nano /etc/systemd/system/siteledger-worker.service
```

Content for service file:
```ini
[Unit]
Description=SiteLedger Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/siteledger/artisan queue:work --sleep=3 --tries=3
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start the service
sudo systemctl daemon-reload
sudo systemctl enable siteledger-worker
sudo systemctl start siteledger-worker
```

## 🔒 Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable SSL/HTTPS
- [ ] Set secure session cookies
- [ ] Configure firewall rules
- [ ] Regular security updates
- [ ] Backup strategy in place
- [ ] Monitor application logs

## 📊 Post-Deployment Verification

### Test the application:
```bash
# Check if application is accessible
curl -I https://yourdomain.com

# Verify database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check logs for errors
tail -f storage/logs/laravel.log
```

### Default Admin Login:
- **Email**: admin@siteledger.com
- **Password**: admin123
- **Role**: admin (full permissions)

### Additional Admin:
- **Email**: gashumba@siteledger.com
- **Password**: password
- **Role**: admin (full permissions)

## 🔄 Maintenance Commands

```bash
# Update application
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Backup database
mysqldump -u username -p siteledger_production > backup_$(date +%Y%m%d).sql

# Monitor logs
tail -f storage/logs/laravel.log
```

## 🆘 Troubleshooting

### Common Issues:
1. **Permissions Error**: Ensure www-data owns files and has proper permissions
2. **500 Error**: Check logs in `storage/logs/laravel.log`
3. **Cache Issues**: Clear all caches: `php artisan optimize:clear`
4. **Asset Issues**: Rebuild assets: `npm run build`

### Log Locations:
- **Laravel Logs**: `/var/www/siteledger/storage/logs/`
- **Apache Logs**: `/var/log/apache2/`
- **Nginx Logs**: `/var/log/nginx/`
- **PHP Logs**: `/var/log/php8.2-fpm.log`

---

🎉 **Congratulations! Your SiteLedger application is now ready for production!**

For support, refer to the Laravel documentation or check the application logs for specific error details.