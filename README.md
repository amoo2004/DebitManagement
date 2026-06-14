# Debt & Loan Management System

A complete enterprise-grade web application for managing customer loans, repayments, debt tracking, and automated SMS reminders.

## Technology Stack

- **Backend:** Laravel 12, PHP 8.2+, MySQL 8+
- **Frontend:** Blade Templates, Bootstrap 5, AdminLTE Dashboard
- **Authentication:** Laravel Breeze + Sanctum
- **SMS:** Twilio & Africa's Talking
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

### 5. Configure SMS (Optional)
Add to `.env`:
```env
# Twilio
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=+1234567890

# OR Africa's Talking
AFRICASTALKING_USERNAME=your_username
AFRICASTALKING_API_KEY=your_api_key
```

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
1. **Dashboard** - Real-time statistics with charts
2. **Customer Management** - CRUD with search and loan history
3. **Loan Management** - Create/edit loans with auto status updates
4. **Payment Management** - Record payments with receipt printing
5. **SMS Notifications** - Auto-send on loan creation, payments, reminders
6. **Reporting** - Daily/weekly/monthly collections with Excel/PDF export
7. **User Management** - Admin-only user creation and role assignment

### SMS Triggers
- New loan created → Customer receives notification
- Payment recorded → Customer receives receipt
- Due date → Automatic reminder SMS
- Overdue → Automatic overdue SMS
- Manual reminder → Send from loan details page

### Background Jobs
- **Every minute:** Process pending SMS queue
- **Daily at 00:00:** Check and mark overdue loans
- **Daily at 08:00:** Send due date reminders

## API Endpoints

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
