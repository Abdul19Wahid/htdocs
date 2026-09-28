@echo off
setlocal
cd /d "%~dp0"
echo ============================================
echo Smart Finance - Local Setup
 echo ============================================
if not exist .env copy .env.example .env
if not exist database\database.sqlite type nul > database\database.sqlite
call composer install
if errorlevel 1 goto :error
php artisan key:generate
if errorlevel 1 goto :error
call npm install
if errorlevel 1 goto :error
call npm run build
if errorlevel 1 goto :error
php artisan migrate:fresh --seed
if errorlevel 1 goto :error
php artisan storage:link
php artisan optimize:clear
echo.
echo Setup complete.
echo Start the app with: php artisan serve
echo Then open: http://127.0.0.1:8000
pause
exit /b 0
:error
echo.
echo Setup stopped because a command failed.
pause
exit /b 1
