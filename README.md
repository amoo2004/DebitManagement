# Debt & Loan Management System

A complete enterprise-grade web application for managing customer loans, repayments, debt tracking, and automated SMS reminders via the Meseji API.

## Technology Stack

- **Backend:** Laravel 12, PHP 8.2+, MySQL 8+
- **Frontend:** Blade Templates, Bootstrap 5, AdminLTE 3 Dashboard
- **Authentication:** Laravel Breeze (session) + Sanctum (API tokens)
- **SMS:** Meseji API (https://meseji.co.tz)
- **Exports:** Laravel Excel (XLSX), DomPDF (PDF)
- **Queue:** Laravel Queue (Database driver)
- **Scheduler:** Laravel Scheduler

## System Requirements

- PHP 8.2+
- MySQL 8+
- Composer 2.x
- Node.js 18+
- PHP Extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD

## Installation

### 1. Clone & Install Dependencies
```bash
git clone <repository-url> debit-management
cd debit-management
composer install
npm install
npm run build
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=debitmanagement
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

### 5. Configure SMS (Optional - via Web UI)
After login, go to **Settings → SMS Settings** and configure:
- **Meseji API Key**
- **Meseji Sender ID**

### 6. Start Development Server
```bash
php artisan serve
```

### 7. Start Queue Worker (Required for SMS)
```bash
php artisan queue:work
```

### 8. Setup Scheduler (On production server)
Add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Default Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Staff | staff@example.com | password |

## Features

### User Roles
- **Admin:** Full system access, user management, settings
- **Staff:** Create loans, record payments, view customers, send SMS

### Core Modules
1. **Dashboard** - Real-time statistics with charts (total debt, overdue count, recent activity, 6-day collections trend)
2. **Customer Management** - CRUD with search, loan history view, PDF export
3. **Loan Management** - Create/edit loans grouped by customer, auto status updates, PDF/Excel export, single loan PDF
4. **Payment Management** - Smart payment allocation (oldest loans first), receipt printing, recalculation on edit/delete
5. **SMS Notifications** - Auto-send via Meseji API on loan creation, payments, reminders; SMS log history
6. **Reporting** - Daily/weekly/monthly collections with Excel and PDF export
7. **User Management** - Admin-only user creation, editing, role assignment, activation/deactivation
8. **Notifications** - In-app notification system with read/unread tracking

### SMS Triggers
- New loan created → Customer receives approval notification
- Payment recorded → Customer receives payment receipt
- Due date → Automatic reminder SMS (daily at 08:00)
- Overdue → Automatic overdue SMS (daily at 00:00)
- Manual reminder → Send from loan details or customer details page

### Background Jobs
- **Every minute:** Process pending SMS queue (`ProcessSmsLogsJob`)
- **Daily at 00:00:** Check and mark overdue loans, send overdue SMS
- **Daily at 08:00:** Send due date reminder SMS
- **Daily at 03:00:** Clean up old SMS logs (keep latest 10 per customer)

## API Endpoints

All endpoints except `/api/login` require `Authorization: Bearer <token>` header.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/login | Login & get token |
| POST | /api/logout | Logout (auth) |
| GET | /api/user | Get current user (auth) |
| GET/POST | /api/customers | List/Create customers (auth) |
| GET/PUT/DELETE | /api/customers/{id} | CRUD customer (auth) |
| GET/POST | /api/loans | List/Create loans (auth) |
| GET/PUT/DELETE | /api/loans/{id} | CRUD loan (auth) |
| GET/POST | /api/payments | List/Create payments (auth) |
| GET/PUT/DELETE | /api/payments/{id} | CRUD payment (auth) |
| GET | /api/reports/summary | Dashboard summary (auth) |
| GET | /api/reports/collections | Collection report (auth) |

## Running Tests
```bash
php artisan test
```

## Deployment

### Production Checklist
1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Configure production database
3. Set up Supervisor for queue worker
4. Set up cron for scheduler
5. Enable HTTPS
6. Set proper file permissions
7. Run `php artisan config:cache` and `php artisan route:cache`

### Directory Permissions (Linux)
```bash
chmod -R 775 storage bootstrap/cache
chmod -R 775 public
```

### Supervisor Configuration
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/worker.log
```

## License
MIT
