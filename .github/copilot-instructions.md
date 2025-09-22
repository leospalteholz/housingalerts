# Housing Alerts Laravel Application

Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the info here.

## Working Effectively

### Bootstrap, Build, and Test the Repository
- **NEVER CANCEL BUILD OR TEST COMMANDS** - Always set timeout to 300+ seconds minimum
- Bootstrap the application:
  - `composer install --no-interaction` -- takes 2 minutes on first install, 2 seconds when dependencies exist. **NEVER CANCEL: Set timeout to 300+ seconds.**
  - `npm install` -- takes 6 seconds. Set timeout to 120 seconds.
  - `cp .env.example .env`
  - `php artisan key:generate`
  - Configure SQLite database: `sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env`
  - Set absolute database path: `sed -i 's|DB_DATABASE=laravel|DB_DATABASE=/home/runner/work/housingalerts/housingalerts/database/database.sqlite|' .env`
  - `touch database/database.sqlite`
  - `php artisan migrate --force` -- takes <1 second
  - `php artisan db:seed --force` -- takes <1 second, adds sample organizations, regions, hearings, and users
- Build frontend assets:
  - `npm run build` -- takes 1.6 seconds. Builds Vite assets with Tailwind CSS.
- Run Laravel Pint for code formatting:
  - `vendor/bin/pint` -- Auto-fixes style issues. Always run before committing.
  - `vendor/bin/pint --test` -- Check for style issues without fixing.

### Running the Application
- **Development Server**:
  - `php artisan serve --host=0.0.0.0 --port=8000` -- Starts development server on http://localhost:8000
  - Server responds correctly with 200 status and Laravel session cookies
- **Email Processing (Required for Notifications)**:
  - `php artisan schedule:work` -- **NEVER CANCEL: Runs indefinitely.** Essential for email notifications.
  - Alternatively: `php artisan schedule:run` -- Run scheduled tasks once
  - View scheduled tasks: `php artisan schedule:list`

### Testing and Validation
- **Email System Testing**:
  - `php artisan notifications:test` -- Test notification queuing and processing
  - `php artisan notifications:process-pending` -- Manually process pending notifications
- **Database Operations**:
  - `php artisan db:show --counts` -- View database tables and row counts
  - `php artisan migrate:refresh --seed` -- Reset database with fresh data
- **PHPUnit Tests**:
  - `vendor/bin/phpunit` -- **WARNING: 18/24 tests fail due to bcrypt config in testing environment.** This is a known Laravel testing issue and does not affect application functionality. **NEVER CANCEL: Set timeout to 600+ seconds.**

## Validation Scenarios
Always test the complete user workflow after making changes:
1. **Application Startup**: Run bootstrap commands, start server, verify http://localhost:8000 returns 200 status
2. **Database Functionality**: Verify `php artisan db:show --counts` shows seeded data (2 organizations, 2 regions, 2 hearings, 3 users)
3. **Email System**: Run `php artisan notifications:test` and verify test notifications are queued
4. **Code Quality**: Run `vendor/bin/pint` to ensure code formatting standards
5. **Build Process**: Run `npm run build` to ensure frontend assets compile successfully

## Common Tasks and Expected Timing

### Build and Deploy Commands
- `composer install`: **NEVER CANCEL - 2+ minutes first time, 2 seconds subsequent runs**
- `npm install`: 6 seconds
- `npm run build`: 1.6 seconds  
- `php artisan migrate`: <1 second
- `vendor/bin/pint`: <5 seconds
- `php artisan key:generate`: <1 second

### Deployment Commands (from deploy.sh)
- `git pull origin main`
- `composer install --optimize-autoloader --no-dev`
- `npm install && npm run build`
- `php artisan migrate --force`
- `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- `chmod -R 775 storage bootstrap/cache`

## Production Environment Setup
- Uses **SQLite database** for easy deployment
- **SendGrid SMTP** for email delivery
- **Laravel Scheduler** must run via cron: `* * * * * cd /path/to/app && php artisan schedule:run`
- Required environment variables:
  - `APP_ENV=production`
  - `APP_DEBUG=false` 
  - `DB_CONNECTION=sqlite`
  - `MAIL_PASSWORD=[sendgrid_api_key]`
  - `MAIL_FROM_ADDRESS="noreply@yourdomain.com"`

## Key Application Features
- **Email Notifications**: Hearing created (1-minute delay) and day-of reminders
- **Region-based Subscriptions**: Users subscribe to specific geographic regions
- **CASL Compliance**: Email consent tracking and logging
- **Multi-organization Support**: Tool can be deployed for different advocacy groups
- **Admin Dashboard**: CRUD operations for hearings, users, regions

## Repository Structure
### Important Directories
- `app/Console/Commands/` -- Custom artisan commands for notifications
- `app/Http/Controllers/` -- Web controllers for admin and public interfaces
- `app/Mail/` -- Email templates and mailable classes
- `app/Models/` -- Eloquent models (User, Hearing, Organization, Region)
- `app/Services/` -- Business logic (NotificationService)
- `database/migrations/` -- Database schema definitions
- `database/seeders/` -- Sample data for development
- `resources/views/` -- Blade templates
- `routes/web.php` -- Web routes

### Key Files
- `deploy.sh` -- Production deployment script
- `cron.php` -- Alternative cron entry point
- `.env.example` -- Environment configuration template
- `phpunit.xml` -- Test configuration
- `vite.config.js` -- Frontend build configuration
- `tailwind.config.js` -- CSS framework configuration

## Known Issues and Workarounds
- **PHPUnit Tests**: 18 out of 24 tests fail due to bcrypt configuration in testing environment. This is a Laravel framework issue and does not affect production functionality.
- **Composer GitHub Rate Limiting**: Use `--no-interaction` flag to avoid prompts for GitHub tokens
- **Database Configuration**: Must use absolute paths for SQLite database in testing/production environments

## CI/CD Validation Requirements
Always run these commands before committing changes:
1. `vendor/bin/pint` -- Fix code style issues
2. `npm run build` -- Ensure frontend builds successfully  
3. `php artisan notifications:test` -- Verify email system works
4. Test development server startup and response

## Documentation References
- See `README.md` for application overview and features
- See `DEPLOYMENT.md` for complete production deployment instructions
- Laravel 12.x documentation for framework-specific questions