@echo off
echo ===============================
echo  KOL COMPOSER SETUP (WINDOWS)
echo ===============================
echo.

REM Go to project folder
cd /d C:\xampp\htdocs\kol

REM Check PHP
php -v >nul 2>&1
if errorlevel 1 (
  echo PHP not found. Install XAMPP properly.
  pause
  exit /b
)

REM Install Composer locally
if not exist composer.phar (
  echo Installing Composer locally...
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php composer-setup.php
  del composer-setup.php
)

REM Install dependencies
php composer.phar require smalot/pdfparser
php composer.phar require phpoffice/phpword

REM Verify
if exist vendor\autoload.php (
  echo SUCCESS: vendor/autoload.php found
) else (
  echo ERROR: vendor folder missing
)

pause
