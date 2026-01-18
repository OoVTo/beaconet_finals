# BEACONET-mini Startup Script for PowerShell

Write-Host "========================================"
Write-Host "BEACONET-mini - Setup & Run" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if vendor directory exists
if (-not (Test-Path vendor)) {
    Write-Host "Installing Composer dependencies..." -ForegroundColor Yellow
    & composer install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Composer installation failed!" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

# Create database file if not exists
if (-not (Test-Path database\database.sqlite)) {
    Write-Host "Creating SQLite database..." -ForegroundColor Yellow
    New-Item -Path database\database.sqlite -ItemType File -Force > $null
}

# Create .env file if not exists
if (-not (Test-Path .env)) {
    Write-Host "Creating .env file..." -ForegroundColor Yellow
    Copy-Item .env.example .env
}

# Generate app key if not set
$envContent = Get-Content .env
if ($envContent -notmatch "^APP_KEY=base64:") {
    Write-Host "Generating application key..." -ForegroundColor Yellow
    & php artisan key:generate
}

# Run migrations
Write-Host "Running database migrations..." -ForegroundColor Yellow
& php artisan migrate --force

# Seed database
Write-Host "Seeding database with admin user..." -ForegroundColor Yellow
& php artisan db:seed --force

# Create storage link
Write-Host "Creating storage link for uploads..." -ForegroundColor Yellow
& php artisan storage:link

# Start the server
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Starting Laravel Development Server..." -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access the application at: http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "Admin login: admin@email.com / admin@123123123" -ForegroundColor Green
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

& php artisan serve

Read-Host "Press Enter to exit"
