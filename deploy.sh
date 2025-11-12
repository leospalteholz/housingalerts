#!/bin/bash

echo "🚀 Starting deployment..."
echo "=================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Make sure you're in the Laravel root directory."
    exit 1
fi

# Pull latest changes from git
echo "📥 Pulling latest changes from git..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "❌ Git pull failed. Please check for conflicts."
    exit 1
fi

# Run the composer install
composer install --optimize-autoloader --no-dev

# Build frontend assets
if [ -f "package.json" ]; then
    echo "🛠️  Building frontend assets..."
    npm install
    npm run build
fi

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Ensure an admin user exists in production
echo "🔐 Ensuring admin user exists..."
php artisan app:create-admin --no-interaction

# Create storage symbolic link (if it doesn't exist)
echo "🔗 Ensuring storage symbolic link..."
if [ -L "public/storage" ]; then
    echo "INFO: public/storage symlink already present."
elif [ -e "public/storage" ]; then
    echo "WARN: public/storage exists but is not a symlink; skipping storage:link."
else
    php artisan storage:link
fi

# Rebuild caches for production
echo "⚡ Rebuilding production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod -R 775 storage bootstrap/cache

# Optional: Clear OPcache if available
if command -v php &> /dev/null; then
    echo "🔄 Clearing OPcache..."
    php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared'; } else { echo 'OPcache not available'; }"
fi

echo "=================================="
echo "✅ Deployment completed successfully!"
echo "🌐 Your application is now updated"
echo "=================================="
