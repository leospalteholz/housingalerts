@echo off
echo 🚀 Starting deployment...
echo ==================================

REM Check if we're in the right directory
if not exist "artisan" (
    echo ❌ Error: artisan file not found. Make sure you're in the Laravel root directory.
    exit /b 1
)

REM Pull latest changes from git
echo 📥 Pulling latest changes from git...
git pull origin main

if %errorlevel% neq 0 (
    echo ❌ Git pull failed. Please check for conflicts.
    exit /b 1
)

REM Check if composer files were updated (simplified check)
echo 📦 Updating composer dependencies...
composer install --optimize-autoloader --no-dev

REM Clear all caches
echo 🧹 Clearing caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

REM Run database migrations
echo 🗄️  Running database migrations...
php artisan migrate --force

REM Rebuild caches for production
echo ⚡ Rebuilding production caches...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ==================================
echo ✅ Deployment completed successfully!
echo 🌐 Your application is now updated
echo ==================================
pause
