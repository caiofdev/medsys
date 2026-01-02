# 🏥 MedSys - Medical System

A comprehensive medical management system built with Laravel, Inertia.js, React, and TypeScript.

## 📋 Table of Contents

- [System Requirements](#system-requirements)
- [Tech Stack](#tech-stack)
- [Installation Methods](#installation-methods)
  - [Option 1: Docker (Recommended)](#option-1-docker-recommended)
  - [Option 2: Local Environment](#option-2-local-environment)
- [Database Access](#database-access)
- [Useful Commands](#useful-commands)
- [Troubleshooting](#troubleshooting)

---

## 🖥️ System Requirements

### For Docker Setup
- **Windows 10/11** (64-bit, Home, Pro, Enterprise, or Education)
- **WSL 2** enabled
- **Docker Desktop** 4.0+ installed and running
- **4GB RAM** minimum (8GB recommended)

### For Local Setup
- **PHP 8.2+** (8.5 recommended)
- **Composer** 2.9+
- **Node.js** 18+ and npm
- **MySQL** 8.0+ or **MariaDB** 10.5+

---

## 🛠️ Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.5 | Backend language |
| Laravel | 12.x | PHP framework |
| Inertia.js | 2.x | SPA adapter |
| React | 19.x | Frontend framework |
| TypeScript | 5.x | Type safety |
| Vite | 6.x | Frontend build tool |
| Tailwind CSS | 4.x | CSS framework |
| MySQL | 8.0 | Database |
| Docker | Latest | Containerization |
| Nginx | Alpine | Web server |

---

## 🚀 Installation Methods

### Option 1: Docker (Recommended)

#### Prerequisites
1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop)
2. Ensure Docker Desktop is running

#### Setup Steps

**Automatic Setup (Windows PowerShell):**
```powershell
.\docker-setup.ps1
```

**Manual Setup:**

1. **Clone and navigate to project:**
```powershell
cd path/to/medsys
```

2. **Copy environment file:**
```powershell
Copy-Item .env.example .env
```

3. **Configure database settings in `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=medsys
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

4. **Build and start containers:**
```powershell
docker-compose up -d --build
```

5. **Install PHP dependencies:**
```powershell
docker-compose exec app composer install
```

6. **Generate application key:**
```powershell
docker-compose exec app php artisan key:generate
```

7. **Run database migrations:**
```powershell
docker-compose exec app php artisan migrate
```

8. **Seed database (optional):**
```powershell
docker-compose exec app php artisan db:seed
```

#### Docker Services

| Service | Container | Host Port | Description |
|---------|-----------|-----------|-------------|
| Nginx | medsys-nginx | 8000 | Web server |
| PHP-FPM | medsys-app | - | Laravel application |
| MySQL | medsys-db | 3307 | Database |
| Node/Vite | medsys-node | 5173 | Frontend dev server |

#### Access Application
- **Application:** http://localhost:8000
- **Vite HMR:** http://localhost:5173
- **MySQL:** localhost:3307

---

### Option 2: Local Environment

#### Prerequisites

**1. Install PHP 8.5:**
- Download from [PHP.net](https://www.php.net/downloads)
- Choose **VS17 x64 Non Thread Safe** for Windows
- Add PHP to system PATH

**2. Enable PHP Extensions in `php.ini`:**
```ini
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=mysqli
extension=fileinfo
extension=zip
extension=curl
```

**3. Install Composer:**
- Download [Composer](https://getcomposer.org/download/)
- Run installer and select your PHP executable

**4. Install Node.js:**
- Download [Node.js 18+](https://nodejs.org/)
- Verify: `node -v` and `npm -v`

**5. Install MySQL:**
- Download [MySQL 8.0](https://dev.mysql.com/downloads/installer/)
- Or use [XAMPP](https://www.apachefriends.org/)
- Configure root password during installation

#### Setup Steps

1. **Create database:**
```sql
CREATE DATABASE medsys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Copy environment file:**
```powershell
Copy-Item .env.example .env
```

3. **Configure database in `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medsys
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

4. **Install PHP dependencies:**
```powershell
composer install
```

5. **Install JavaScript dependencies:**
```powershell
npm install
```

6. **Generate application key:**
```powershell
php artisan key:generate
```

7. **Run migrations:**
```powershell
php artisan migrate
```

8. **Seed database (optional):**
```powershell
php artisan db:seed
```

9. **Start development servers:**

**Terminal 1 - Laravel:**
```powershell
php artisan serve
```

**Terminal 2 - Vite:**
```powershell
npm run dev
```

#### Access Application
- **Application:** http://localhost:8000

---

## 🗄️ Database Access

### Option 1: MySQL Workbench (GUI)
1. Open MySQL Workbench
2. Create new connection:
   - **Docker:** Host: `127.0.0.1`, Port: `3307`
   - **Local:** Host: `127.0.0.1`, Port: `3306`
   - Username: `root`
   - Password: (your configured password)

### Option 2: Command Line (Docker)
```powershell
docker-compose exec db mysql -u root -p
```

### Option 3: Laravel Tinker
```powershell
# Docker
docker-compose exec app php artisan tinker

# Local
php artisan tinker
```

---

## 📝 Useful Commands

### Docker Environment

**Container Management:**
```powershell
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Restart containers
docker-compose restart

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
```

**Laravel Artisan:**
```powershell
docker-compose exec app php artisan [command]

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller UserController
docker-compose exec app php artisan route:list
docker-compose exec app php artisan cache:clear
```

**Composer:**
```powershell
docker-compose exec app composer install
docker-compose exec app composer update
docker-compose exec app composer require package/name
```

**NPM:**
```powershell
docker-compose exec node npm install
docker-compose exec node npm run build
```

**Database:**
```powershell
# Backup
docker-compose exec db mysqldump -u root -p medsys > backup.sql

# Restore
docker-compose exec -T db mysql -u root -p medsys < backup.sql

# Fresh migration
docker-compose exec app php artisan migrate:fresh --seed
```

### Local Environment

**Development:**
```powershell
# Run Laravel server
php artisan serve

# Run Vite dev server
npm run dev

# Run both (using composer script)
composer dev
```

**Laravel Artisan:**
```powershell
php artisan migrate
php artisan db:seed
php artisan make:controller UserController
php artisan route:list
php artisan cache:clear
php artisan config:clear
```

**Composer:**
```powershell
composer install
composer update
composer require package/name
```

**NPM:**
```powershell
npm install
npm run dev
npm run build
npm run lint
```

---

## 🔧 Troubleshooting

### Docker Issues

**Port Already in Use:**
Edit `docker-compose.yml` to change ports:
```yaml
nginx:
  ports:
    - "8080:80"  # Change 8000 to 8080
```

**MySQL Container Restarting:**
Check logs and verify `.env` configuration:
```powershell
docker-compose logs db
```

**Rebuild from Scratch:**
```powershell
docker-compose down -v
docker-compose up -d --build
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
- [React Documentation](https://react.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Docker Documentation](https://docs.docker.com/)