# Docker initial setup script for MedSys

Write-Host "🐳 Configuring Docker environment for MedSys..." -ForegroundColor Cyan

# Check if Docker is installed
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Docker is not installed. Please install Docker Desktop first." -ForegroundColor Red
    Write-Host "Download: https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Docker detected" -ForegroundColor Green

# Check if .env exists
if (-not (Test-Path ".env")) {
    Write-Host "📝 Creating .env file..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
}

# Update .env settings for Docker
Write-Host "🔧 Updating .env settings for Docker..." -ForegroundColor Yellow

$envContent = Get-Content ".env" -Raw
$envContent = $envContent -replace "DB_HOST=127.0.0.1", "DB_HOST=db"
$envContent = $envContent -replace "DB_PORT=3306", "DB_PORT=3306"
Set-Content ".env" $envContent

Write-Host "✅ .env file configured" -ForegroundColor Green

# Build containers
Write-Host "🏗️  Building Docker containers..." -ForegroundColor Cyan
docker-compose build

# Start containers
Write-Host "🚀 Starting containers..." -ForegroundColor Cyan
docker-compose up -d

# Wait for MySQL to initialize
Write-Host "⏳ Waiting for MySQL to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Install Composer dependencies
Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Cyan
docker-compose exec -T app composer install

# Generate application key
Write-Host "🔑 Generating application key..." -ForegroundColor Cyan
docker-compose exec -T app php artisan key:generate

# Run migrations
Write-Host "🗃️  Running migrations..." -ForegroundColor Cyan
docker-compose exec -T app php artisan migrate --force

# Run seeders
Write-Host "🌱 Seeding database..." -ForegroundColor Cyan
docker-compose exec -T app php artisan db:seed --force

Write-Host ""
Write-Host "✅ Setup completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Access the application at: http://localhost:8000" -ForegroundColor Cyan
Write-Host "📍 Vite dev server: http://localhost:5173" -ForegroundColor Cyan
Write-Host ""
Write-Host "Useful commands:" -ForegroundColor Yellow
Write-Host "  docker-compose up -d       # Start containers" -ForegroundColor White
Write-Host "  docker-compose down        # Stop containers" -ForegroundColor White
Write-Host "  docker-compose logs -f     # View logs" -ForegroundColor White
Write-Host "  docker-compose exec app php artisan [command]  # Execute artisan" -ForegroundColor White
