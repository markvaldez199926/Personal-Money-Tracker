# Personal Money Tracker 💰

A high-performance, full-stack personal finance application built with **Laravel 12**, **Livewire 3**, **Alpine.js**, and **Tailwind CSS**. Designed for effortless tracking of daily income, expenses, budgets, wallets, recurring subscriptions, and analytics.

---

## 🚀 Architecture & Server Stack

This application comes ready for containerized production deployment using **Docker** and **Docker Compose**:

- **Nginx 1.25 Alpine**: High-speed reverse proxy with static asset caching, Gzip compression, and security headers.
- **PHP 8.2 FPM Alpine**: Multi-stage production container with OPcache, optimized autoloader, and compiled Vite assets.
- **MySQL 8.0**: Relational database with persistent storage (`db_data`) and healthchecks.
- **Queue Worker**: Dedicated background worker container (`queue:work`) for asynchronous tasks.
- **Cron Scheduler**: Dedicated artisan scheduler container (`schedule:work`) for automated recurring transaction execution.

```
                           +-------------------------------+
                           |     Browser / Client          |
                           +---------------+---------------+
                                           | Port 8000
                                           v
                           +---------------+---------------+
                           |     Nginx (Web Proxy)         |
                           +---------------+---------------+
                                           | FastCGI :9000
                                           v
+------------------------+ +---------------+---------------+ +------------------------+
|   Queue Worker         | |   PHP-FPM Application         | |   Artisan Scheduler    |
|   (php artisan queue)  | |   (money_tracker_app)         | |   (schedule:work)      |
+-----------+------------+ +---------------+---------------+ +-----------+------------+
            |                              |                             |
            +------------------------------+-----------------------------+
                                           |
                                           v
                           +---------------+---------------+
                           |      MySQL 8.0 Database       |
                           |      (db_data volume)         |
                           +-------------------------------+
```

---

## 🐳 Quick Start with Docker (Production Server)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS) or Docker Engine + Docker Compose (Linux).

### 1-Click Launch

**Windows:**
Double click `docker-start.bat` or run:
```cmd
docker-start.bat
```

**Linux / macOS:**
```bash
chmod +x docker-start.sh
./docker-start.sh
```

**Or standard Docker Compose commands:**
```bash
cp .env.docker.example .env.docker
docker compose up -d --build
```

Access the application at: **[http://localhost:8000](http://localhost:8000)**

### Helpful Container Commands
```bash
# View running container status
docker compose ps

# View live container logs
docker compose logs -f

# Run Artisan commands inside the app container
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed

# Stop containers
docker compose down
```

---

## 💻 Local Development (Native without Docker)

If you prefer running directly on your host machine:

### 1. Requirements
- PHP >= 8.2 with `pdo_sqlite` or `pdo_mysql`
- Composer >= 2.x
- Node.js >= 20.x

### 2. Setup
```bash
composer install
npm install
npm run build
php artisan migrate
```

### 3. Start Development Server
```bash
php artisan serve --port=8000
```
Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 🧪 Testing

Execute the comprehensive test suite (Unit & Feature tests):
```bash
php artisan test
```

---

## 🔒 Security & Optimization
- **Production OPcache**: JIT-compiled bytecode with disabled file stat timestamp checking in production.
- **CSRF & Atomic Transactions**: Livewire tokens and database transactions ensure financial integrity.
- **Automated Recurring Engine**: Runs daily at `00:01` with `withoutOverlapping()` protection.
