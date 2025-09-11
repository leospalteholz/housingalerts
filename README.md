# Housing Alerts

Housing Alerts is a simple advocacy tool to help housing organizations notify supporters about upcoming **public hearings**.  
Supporters can sign up for updates in specific **regions**, and administrators can publish hearings that automatically trigger email notifications.  

The goal is to make this tool easy to deploy for any housing advocacy group with **minimal cost and setup**.  

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

## 🛠️ Stack

- **Framework**: Laravel 10.x (PHP)  
- **Database**: MySQL (Cloudways)  
- **Email**: SendGrid SMTP  
- **Hosting**: Cloudways (PHP + MySQL ready)  

---

## 🚦 Development Workflow

1. Clone repo / set up Laravel on Cloudways.  
2. Copy `.env.example` → `.env` and configure DB + SendGrid.  
3. Run migrations:  
   ```bash
   php artisan migrate
