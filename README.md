# Housing Alerts

Housing Alerts is a Laravel-based advocacy tool that helps housing organizations notify supporters about upcoming **public hearings**. Supporters can subscribe to updates for specific **regions**, and administrators can publish hearings that automatically trigger email notifications.

The system features automated email notifications with 1-minute delays for new hearings and day-of reminders, all while maintaining CASL compliance for Canadian email regulations.

---

## 🚀 Key Features

- **Region-based subscriptions**: Users subscribe to specific geographic regions
- **Automated email notifications**: Hearing created alerts and day-of reminders
- **CASL compliance**: Proper consent tracking and email record retention
- **Multi-organization support**: Tool can be deployed for different advocacy groups
- **Professional email templates**: Responsive HTML emails with unsubscribe functionality
- **Notification preferences**: Users control what types of emails they receive
- **Duplicate prevention**: Smart system prevents sending duplicate notifications

---

## 📧 Email Notification System

### Notification Types
1. **Hearing Created**: Sent 1 minute after a hearing is published
2. **Day-of Reminder**: Sent on the morning of the hearing date

### Processing
- **Laravel Task Scheduler**: Runs every minute to process pending notifications
- **Queue System**: Handles email sending asynchronously with failure tracking
- **Smart Timing**: Configurable delays prevent spam and allow for corrections

### CASL Compliance
- **Consent Tracking**: Records user opt-in status for each notification
- **Email Logging**: Maintains 3-year record of all communications
- **Easy Unsubscribe**: One-click unsubscribe in all emails  

---

## 🚀 Purpose

- Allow community members to **subscribe** to updates for their region.  
- Allow administrators to **publish hearings** (title, description, optional image).  
- Automatically **notify subscribers via email** (powered by SendGrid SMTP).  
- Support **multiple organizations**, so the tool can be reused by groups in different cities.  
- Provide **unsubscribe** functionality to respect user choice and email regulations.  

---

## 🏗️ Architecture

### Database Schema

**Organizations**  
- `id`  
- `name`  
- `slug` (unique identifier for URLs)  
- `contact_email`  

**Users**  
- `id`  
- `name`  
- `email`  
- `password` (hashed, for admin users)  
- `organization_id` (FK → organizations)  
- `unsubscribed_at` (timestamp, null if active)  

**Regions**  
- `id`  
- `organization_id` (FK → organizations)  
- `name`  

**Hearings**  
- `id`  
- `organization_id` (FK → organizations)  
- `region_id` (FK → regions)  
- `title`  
- `body` (hearing details)  
- `image_url` (optional)  

**User_Region (pivot table)**  
- `id`  
- `user_id` (FK → users)  
- `region_id` (FK → regions)  

---

## 🔑 Relationships

- **Organization** has many **Users**, **Regions**, and **Hearings**.  
- **User** belongs to one **Organization** and may subscribe to many **Regions**.  
- **Region** belongs to one **Organization**, has many **Hearings**, and has many **Users** (via `user_region`).  
- **Hearing** belongs to one **Organization** and one **Region**.  

---

## ⚙️ Major Components

### 1. **Authentication**
- Laravel’s built-in authentication system (`User` model).  
- Admin users can log in to manage hearings.  

### 2. **Public Signup**
- Supporters provide **email** and select **regions**.  
- Stored in the `users` table with region subscriptions in `user_region`.  

### 3. **Admin Dashboard**
- CRUD for **hearings**.  
- When a hearing is created, all users subscribed to the matching region are emailed.  

### 4. **Email Notifications**
- Emails sent through **SendGrid SMTP**.  
- Each email includes an **unsubscribe link**.  
- Templates handled with Laravel **Mailable classes** and Blade views.  

### 5. **Unsubscribe Flow**
- Clicking unsubscribe marks `unsubscribed_at` on the `users` table.  
- User will no longer receive notifications.  

---

## 🔮 Future Enhancements

- Multi-organization onboarding wizard.  
- Region hierarchy (province → city → neighborhood).  
- Webhooks to ingest public notices automatically.  
- Analytics: track open/click rates for hearings.  
- Support for SMS notifications.  

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12.x (PHP 8.2+)
- **Database**: MySQL with foreign key constraints
- **Email Service**: SendGrid SMTP
- **Task Scheduling**: Laravel Scheduler with cron
- **Frontend**: Blade templates with Tailwind CSS
- **Hosting**: Cloudways (optimized for Laravel)

---

## � Quick Start

### Local Development

1. **Clone and setup**:
   ```bash
   git clone https://github.com/leospalteholz/housingalerts.git
   cd housingalerts
   composer install
   npm install && npm run build
   ```

2. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Setup database**:
   ```bash
   php artisan migrate
   php artisan db:seed  # Optional: adds sample data
   ```

4. **Start scheduler** (for email processing):
   ```bash
   php artisan schedule:work
   ```

### Testing Email System

```bash
# Test the notification system
php artisan notifications:test

# Process pending notifications manually
php artisan notifications:process-pending
```

---

## 🌐 Production Deployment

### Cloudways Deployment

For complete deployment instructions, see **[DEPLOYMENT.md](DEPLOYMENT.md)**.

#### Quick Deploy Steps:

1. **Server Setup**: Create Laravel application on Cloudways
2. **Code Deploy**: Clone repository to server
3. **Environment**: Configure `.env` with database and SendGrid credentials
4. **Database**: Run migrations with `php artisan migrate --force`
5. **Scheduler**: Add cron job for `php artisan schedule:run`
6. **SSL**: Configure domain and enable SSL certificate

#### Required Environment Variables:

```env
# Database (from Cloudways dashboard)
DB_HOST=[cloudways_db_host]
DB_DATABASE=[cloudways_db_name] 
DB_USERNAME=[cloudways_db_user]
DB_PASSWORD=[cloudways_db_password]

# SendGrid Email
MAIL_PASSWORD=[sendgrid_api_key]
MAIL_FROM_ADDRESS="noreply@yourdomain.com"

# Production settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### Cron Job Configuration:

Add to Cloudways cron jobs:
```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

This enables automatic email processing every minute.

---

## 📧 Email System Overview

### Notification Workflow

1. **Admin creates hearing** → System queues notifications for subscribed users
2. **Laravel Scheduler** → Processes queued emails based on timing rules
3. **Email delivery** → SendGrid handles actual email delivery
4. **Status tracking** → System logs success/failure for CASL compliance

### Timing Rules

- **Hearing Created**: 1-minute delay allows for corrections before sending
- **Day-of Reminder**: Automatically sent on the morning of hearing date
- **Duplicate Prevention**: Smart system prevents multiple emails to same user

### CASL Compliance Features

- ✅ **Consent Tracking**: Records user opt-in for each notification type
- ✅ **Email Logging**: Maintains detailed records for 3+ years
- ✅ **Easy Unsubscribe**: One-click unsubscribe in every email
- ✅ **Preference Management**: Users control notification types

---

## 🔧 Administration

### Creating Hearings

When administrators create hearings through the admin interface:

1. **Immediate**: Day-of reminder notifications are queued
2. **1-minute delay**: Hearing created notifications are queued and sent
3. **Automatic**: Users receive professional HTML emails with hearing details

### Monitoring

```bash
# Check notification status
php artisan db:show --counts

# View recent email logs
php artisan notifications:process-pending --dry-run

# Test email functionality
php artisan notifications:test
```

---

## 🔮 Future Enhancements

- **Analytics Dashboard**: Track email open rates and user engagement
- **SMS Notifications**: Optional text message alerts
- **Calendar Integration**: iCal attachments for hearing reminders
- **Multi-language Support**: French/English templates for Canadian compliance
- **Advanced Scheduling**: Custom timing rules for different hearing types
- **Webhook Integration**: Automatic hearing imports from government APIs

---

## 📞 Support

- **Deployment Issues**: See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed instructions
- **Email Configuration**: Check SendGrid setup and API key configuration
- **Database Issues**: Verify Cloudways database credentials and connectivity
- **Cron Jobs**: Ensure Laravel scheduler is running via cron

For technical support, check the application logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 📄 License

This project is open-source software licensed under the [MIT license](LICENSE).
