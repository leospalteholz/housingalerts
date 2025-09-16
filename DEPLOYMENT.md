# Cloudways Deployment Guide

This guide walks you through deploying the Housing Alerts application to a Cloudways server.

## Prerequisites

- Cloudways account with a server running PHP 8.2+ and MySQL
- SSH access to your Cloudways server
- Git configured on your server
- Composer installed on your server

## 1. Server Setup

### 1.1 Create Application on Cloudways

1. Log into your Cloudways dashboard
2. Create a new application:
   - **Application Name**: `housingalerts`
   - **Project Name**: Your project name
   - **PHP Version**: 8.2 or higher
   - **Application Type**: Laravel

### 1.2 Configure Server Settings

1. **PHP Settings**:
   - Max execution time: 300 seconds
   - Memory limit: 512M
   - Upload max filesize: 64M

2. **Database**:
   - Note your database credentials from the Cloudways dashboard
   - Database name, username, password, and host

## 2. Code Deployment

### 2.1 Connect via SSH

```bash
ssh [master_username]@[server_ip] -p [port]
cd applications/[app_folder]/public_html
```

### 2.2 Clone Repository

```bash
# Remove default files
rm -rf *
rm -rf .*  # Be careful with this command

# Clone your repository
git clone https://github.com/leospalteholz/housingalerts.git .

# Or if using SSH
git clone git@github.com:leospalteholz/housingalerts.git .
```

### 2.3 Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies (if you have frontend assets)
npm install
npm run build
```

## 3. Environment Configuration

### 3.1 Create Environment File

```bash
cp .env.example .env
nano .env
```

### 3.2 Configure Environment Variables

Update `.env` with your Cloudways settings:

```env
APP_NAME="Housing Alerts"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database (from Cloudways dashboard)
DB_CONNECTION=mysql
DB_HOST=[your_db_host]
DB_PORT=3306
DB_DATABASE=[your_db_name]
DB_USERNAME=[your_db_username]
DB_PASSWORD=[your_db_password]

# Session and Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# SendGrid Email Settings
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=[your_sendgrid_api_key]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Additional settings
BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
```

### 3.3 Generate Application Key

```bash
php artisan key:generate
```

## 4. Database Setup

### 4.1 Run Migrations

```bash
# Run all migrations
php artisan migrate --force

# Seed initial data (if you have seeders)
php artisan db:seed --force
```

### 4.2 Verify Database

```bash
# Check database tables
php artisan db:show --counts
```

## 5. File Permissions

```bash
# Set correct permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

## 6. Cron Job Setup

### 6.1 Configure Laravel Scheduler

In Cloudways dashboard:

1. Go to **Server Management** → **Cron Job Management**
2. Add a new cron job:
   - **Command**: `cd /home/[master_username]/applications/[app_folder]/public_html && php artisan schedule:run >> /dev/null 2>&1`
   - **Frequency**: Every minute (`* * * * *`)

This enables:
- Email notification processing
- Any other scheduled tasks

## 7. SSL and Domain Setup

### 7.1 Configure Domain

1. In Cloudways dashboard, go to **Domain Management**
2. Add your domain
3. Enable SSL certificate (Let's Encrypt)

### 7.2 Update Application URL

Update your `.env` file:
```env
APP_URL=https://yourdomain.com
```

## 8. Email Configuration

### 8.1 SendGrid Setup

1. Create SendGrid account
2. Generate API key
3. Add API key to `.env` file
4. Verify sender identity in SendGrid

### 8.2 Test Email

```bash
# Test email functionality
php artisan notifications:test
```

## 9. Performance Optimization

### 9.1 Cache Configuration

```bash
# Cache routes and config for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9.2 Optimize Autoloader

```bash
composer dump-autoload --optimize
```

## 10. Monitoring and Maintenance

### 10.1 Log Monitoring

Monitor logs at:
```bash
tail -f storage/logs/laravel.log
```

### 10.2 Database Backup

Set up regular database backups in Cloudways dashboard:
1. Go to **Backup & Restore**
2. Configure automatic backups

### 10.3 Health Checks

Create a simple health check endpoint to monitor your application.

## 11. Deployment Script

Create a deployment script for updates:

```bash
#!/bin/bash
# deploy.sh

echo "Starting deployment..."

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment complete!"
```

Make it executable:
```bash
chmod +x deploy.sh
```

## Troubleshooting

### Common Issues

1. **500 Error**: Check file permissions and `.env` configuration
2. **Database Connection**: Verify database credentials in Cloudways dashboard
3. **Email Not Sending**: Check SendGrid configuration and API key
4. **Cron Jobs Not Running**: Verify cron job syntax and file paths

### Debug Commands

```bash
# Check application status
php artisan about

# Check configuration
php artisan config:show

# Check database connection
php artisan db:show

# Check email configuration
php artisan notifications:test
```

## Security Checklist

- [ ] `.env` file is not in version control
- [ ] `APP_DEBUG=false` in production
- [ ] SSL certificate is active
- [ ] Database credentials are secure
- [ ] SendGrid API key is secure
- [ ] File permissions are correct
- [ ] Regular backups are configured

## Support

For issues specific to:
- **Cloudways**: Contact Cloudways support
- **Laravel**: Check Laravel documentation
- **Application**: Check application logs and GitHub issues
