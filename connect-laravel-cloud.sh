#!/bin/bash

# Laravel Cloud Database Connection Test Script

echo "🔗 Testing Laravel Cloud Database Connection..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Database credentials from Laravel Cloud
DB_HOST="db-a046ea68-d9fc-4c7a-bee2-a210e6b114d2.us-east-2.public.db.laravel.cloud"
DB_PORT="3306"
DB_DATABASE="main"
DB_USERNAME="siteledger"

echo -e "${YELLOW}Please enter your Laravel Cloud database password:${NC}"
read -s DB_PASSWORD

echo -e "${YELLOW}Testing connection with MySQL client...${NC}"

# Test connection with mysql client
mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT 'Connection successful!' as status, NOW() as timestamp;"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database connection successful!${NC}"
    
    # Update .env file
    echo -e "${YELLOW}Updating .env file with Laravel Cloud credentials...${NC}"
    
    # Backup current .env
    cp .env .env.backup
    
    # Update .env file
    sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
    sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
    
    echo -e "${GREEN}✅ .env file updated successfully!${NC}"
    
    # Test Laravel connection
    echo -e "${YELLOW}Testing Laravel database connection...${NC}"
    php artisan tinker --execute="
    try {
        DB::connection()->getPdo();
        echo 'Laravel database connection successful!\n';
        echo 'Database: ' . config('database.connections.mysql.database') . '\n';
        echo 'Host: ' . config('database.connections.mysql.host') . '\n';
    } catch(Exception \$e) {
        echo 'Laravel connection failed: ' . \$e->getMessage() . '\n';
    }"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}🎉 Laravel Cloud database is ready to use!${NC}"
        
        # Run migrations
        echo -e "${YELLOW}Running database migrations...${NC}"
        php artisan migrate --force
        
        echo -e "${GREEN}🚀 Database setup complete!${NC}"
    else
        echo -e "${RED}❌ Laravel connection failed. Check your .env configuration.${NC}"
    fi
    
else
    echo -e "${RED}❌ Database connection failed. Please check your credentials.${NC}"
    echo -e "${YELLOW}Make sure you have the correct password from your Laravel Cloud dashboard.${NC}"
fi