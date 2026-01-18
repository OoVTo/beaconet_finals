@echo off
REM BEACONET-mini Startup Script for Windows

echo.
echo ========================================
echo BEACONET-mini - Setup & Run
echo ========================================
echo.

REM Check if vendor directory exists
if not exist vendor\ (
    echo Installing Composer dependencies...
    composer install
    if errorlevel 1 (
        echo Composer installation failed!
        pause
        exit /b 1
    )
)

REM Check if database exists
if not exist database\database.sqlite (
    echo Creating SQLite database...
    type nul > database\database.sqlite
)

REM Generate app key if not exists
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
)

REM Check if APP_KEY is set
findstr /m "^APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo Generating application key...
    php artisan key:generate
)

REM Run migrations
echo Running database migrations...
php artisan migrate --force

REM Seed database (creates admin user)
echo Seeding database...
php artisan db:seed --force

REM Create storage link
echo Creating storage link...
php artisan storage:link

REM Start the server
echo.
echo ========================================
echo Starting Laravel Server...
echo ========================================
echo.
echo Access the application at: http://127.0.0.1:8000
echo Admin login: admin@email.com / admin@123123123
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve

pause
