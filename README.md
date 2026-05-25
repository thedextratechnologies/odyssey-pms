# Odyssey Elevators — Proposal Management System
### Module 1: Authentication + User Management

**Tech Stack:** PHP 8.2 · Laravel 11 · MySQL 8.0 · Blade + Alpine.js · Tailwind CSS CDN

---

## 📁 Delivered Files in This Module

```
database/
  migrations/
    2024_01_01_000001_create_roles_table.php
    2024_01_01_000002_create_territories_table.php
    2024_01_01_000003_create_users_table.php
    2024_01_01_000004_create_audit_logs_table.php
  seeders/
    DatabaseSeeder.php
    RoleSeeder.php
    TerritorySeeder.php        ← 10 states, districts, cities seeded
    AdminUserSeeder.php

app/
  Models/
    Role.php                   ← Role constants + level system
    Territory.php              ← State > District > City hierarchy
    User.php                   ← Full auth model + territory scoping
    AuditLog.php               ← Static record() helper
  Http/
    Controllers/
      Auth/
        LoginController.php    ← Login, logout, change password
        ForgotPasswordController.php
      Admin/
        UserController.php     ← Full CRUD + territory-scoped access
    Middleware/
      MustChangePassword.php
      CanManageUsers.php
  Services/
    UserService.php            ← Create user + temp password + email

resources/views/
  layouts/app.blade.php        ← Main sidebar layout (Alpine.js + Tailwind)
  auth/
    login.blade.php
    change-password.blade.php  ← Force change on first login
  admin/users/
    index.blade.php            ← User list with filters + stats
    create.blade.php           ← Dynamic territory cascade dropdowns
  dashboard.blade.php          ← Role-aware KPI dashboard
  coming-soon.blade.php        ← Placeholder for future modules

routes/web.php
```

---

## 🚀 Installation Steps

### 1. Create a new Laravel 11 project

```bash
composer create-project laravel/laravel odyssey-pms
cd odyssey-pms
```

### 2. Copy all files from this delivery

Copy all files into their respective paths within the Laravel project root.

### 3. Configure `.env`

```env
APP_NAME="Odyssey PMS"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=odyssey_pms
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=pms@odysseyelevators.com
MAIL_FROM_NAME="Odyssey PMS"

SESSION_DRIVER=database
SESSION_LIFETIME=60
```

### 4. Create the MySQL database

```sql
CREATE DATABASE odyssey_pms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 6. Register middleware in bootstrap/app.php

```php
// In bootstrap/app.php, inside withMiddleware():
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'must.change.password' => \App\Http\Middleware\MustChangePassword::class,
        'can.manage.users'     => \App\Http\Middleware\CanManageUsers::class,
    ]);
})
```

### 7. Create email views (optional, for welcome/reset emails)

Create `resources/views/emails/welcome.blade.php` and
`resources/views/emails/password-reset-admin.blade.php`
with the `$user` and `$password` variables available.

### 8. Start the development server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔑 Default Login

| Field    | Value                              |
|----------|------------------------------------|
| Email    | admin@odysseyelevators.com         |
| Password | Odyssey@Admin2026                  |

> ⚠️ You will be forced to change this password on first login.

---

## 🏗️ What's Built (Module 1)

| Feature                        | Status |
|-------------------------------|--------|
| Login with rate limiting       | ✅ Done |
| Account lockout (5 attempts)   | ✅ Done |
| Force password change          | ✅ Done |
| Forgot / Reset password        | ✅ Done |
| 5 roles with level system      | ✅ Done |
| Territory hierarchy (10 states)| ✅ Done |
| User CRUD (Super Admin)        | ✅ Done |
| Territory-scoped user list     | ✅ Done |
| Dynamic district/city dropdowns| ✅ Done |
| Audit logging on all actions   | ✅ Done |
| Role-aware sidebar navigation  | ✅ Done |
| Responsive Blade + Alpine.js UI| ✅ Done |

---

## 📦 Coming Modules

| Module | Description |
|--------|-------------|
| **Module 2** | Leads, Customers, Franchise Management, Follow-ups |
| **Module 3** | Products & Pricing Master, Quotation Engine, Line Items |
| **Module 4** | Approval Workflow (BDE → BDM → ZM → SD) + Notifications |
| **Module 5** | PDF Quotation Generator (branded Odyssey format) |
| **Module 6** | Dashboards, Reports, Excel/PDF Export |

---

## 🗄️ Database Schema Overview

```
roles              → 5 roles with hierarchy levels
territories        → State > District > City (3-level)
users              → Staff with role + territory + manager
audit_logs         → Every action tracked with old/new values
```

---

*Odyssey Elevators Pvt Ltd — Proposal Management System*
*Built with PHP Laravel 11 + MySQL*
