# Script de configuração inicial do Docker para MedSys

Write-Host "🐳 Configurando ambiente Docker para MedSys..." -ForegroundColor Cyan

# Verificar se Docker está instalado
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Docker não está instalado. Instale o Docker Desktop primeiro." -ForegroundColor Red
    Write-Host "Download: https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Docker detectado" -ForegroundColor Green

# Verificar se .env existe
if (-not (Test-Path ".env")) {
    Write-Host "📝 Criando arquivo .env..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
}

# Atualizar configurações do .env para Docker
Write-Host "🔧 Atualizando configurações do .env para Docker..." -ForegroundColor Yellow

$envContent = Get-Content ".env" -Raw
$envContent = $envContent -replace "DB_HOST=127.0.0.1", "DB_HOST=db"
$envContent = $envContent -replace "DB_PORT=3306", "DB_PORT=3306"
Set-Content ".env" $envContent

Write-Host "✅ Arquivo .env configurado" -ForegroundColor Green

# Build dos containers
Write-Host "🏗️  Construindo containers Docker..." -ForegroundColor Cyan
docker-compose build

# Iniciar containers
Write-Host "🚀 Iniciando containers..." -ForegroundColor Cyan
docker-compose up -d

# Aguardar MySQL inicializar
Write-Host "⏳ Aguardando MySQL inicializar..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Instalar dependências do Composer
Write-Host "📦 Instalando dependências do Composer..." -ForegroundColor Cyan
docker-compose exec -T app composer install

# Gerar chave da aplicação
Write-Host "🔑 Gerando chave da aplicação..." -ForegroundColor Cyan
docker-compose exec -T app php artisan key:generate

# Executar migrations
Write-Host "🗃️  Executando migrations..." -ForegroundColor Cyan
docker-compose exec -T app php artisan migrate --force

# Executar seeders
Write-Host "🌱 Populando banco de dados..." -ForegroundColor Cyan
docker-compose exec -T app php artisan db:seed --force

Write-Host ""
Write-Host "✅ Configuração concluída com sucesso!" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Acesse a aplicação em: http://localhost:8000" -ForegroundColor Cyan
Write-Host "📍 Vite dev server: http://localhost:5173" -ForegroundColor Cyan
Write-Host ""
Write-Host "Comandos úteis:" -ForegroundColor Yellow
Write-Host "  docker-compose up -d       # Iniciar containers" -ForegroundColor White
Write-Host "  docker-compose down        # Parar containers" -ForegroundColor White
Write-Host "  docker-compose logs -f     # Ver logs" -ForegroundColor White
Write-Host "  docker-compose exec app php artisan [comando]  # Executar artisan" -ForegroundColor White
