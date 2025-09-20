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

# Always run composer install if vendor does not exist
if [ ! -d "vendor" ]; then
    echo "📦 vendor directory missing, running composer install..."
    composer install --optimize-autoloader --no-dev
# Otherwise, only run composer if composer.json or composer.lock changed
elif git diff HEAD~1 HEAD --name-only | grep -q "composer.json\|composer.lock"; then
    echo "📦 Composer files changed, updating dependencies..."
    composer install --optimize-autoloader --no-dev
else
    echo "📦 No composer changes detected, skipping dependency update"
fi

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
