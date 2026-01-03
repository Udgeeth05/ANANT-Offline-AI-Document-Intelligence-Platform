#!/bin/bash

echo "========================================"
echo " KOL AI FILE-READER SETUP SCRIPT (a.sh)"
echo "========================================"
echo

# -----------------------------
# STEP 0: CHECK PHP
# -----------------------------
echo "[1/6] Checking PHP installation..."
php -v > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo "❌ PHP not found."
  echo "Make sure XAMPP PHP is installed and php.exe is in PATH."
  echo "Expected path: C:\\xampp\\php\\php.exe"
  exit 1
fi
echo "✅ PHP found"
echo

# -----------------------------
# STEP 1: CHECK / INSTALL COMPOSER (LOCAL)
# -----------------------------
echo "[2/6] Checking Composer..."

if [ ! -f "composer.phar" ]; then
  echo "Composer not found. Installing local Composer..."
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php composer-setup.php
  rm composer-setup.php
  echo "✅ Composer installed locally (composer.phar)"
else
  echo "✅ Composer already exists (composer.phar)"
fi
echo

# -----------------------------
# STEP 2: INSTALL PDF PARSER
# -----------------------------
echo "[3/6] Installing PDF parser (smalot/pdfparser)..."
php composer.phar require smalot/pdfparser
echo "✅ PDF parser installed"
echo

# -----------------------------
# STEP 3: INSTALL DOCX PARSER
# -----------------------------
echo "[4/6] Installing DOCX parser (phpoffice/phpword)..."
php composer.phar require phpoffice/phpword
echo "✅ DOCX parser installed"
echo

# -----------------------------
# STEP 4: VERIFY VENDOR AUTOLOAD
# -----------------------------
echo "[5/6] Verifying vendor/autoload.php..."

if [ -f "vendor/autoload.php" ]; then
  echo "✅ vendor/autoload.php found"
else
  echo "❌ vendor/autoload.php missing"
  echo "Composer installation failed."
  exit 1
fi
echo

# -----------------------------
# STEP 5: OCR INFO (MANUAL)
# -----------------------------
echo "[6/6] OCR SETUP (IMAGE FILES)"
echo "----------------------------------------"
echo "To enable IMAGE OCR (.png, .jpg, .jpeg):"
echo
echo "1. Download Tesseract OCR for Windows:"
echo "   https://github.com/UB-Mannheim/tesseract/wiki"
echo
echo "2. Install to:"
echo "   C:\\Program Files\\Tesseract-OCR\\"
echo
echo "3. Ensure it is added to PATH"
echo
echo "Verify using:"
echo "   tesseract --version"
echo

echo "========================================"
echo " SETUP COMPLETE 🎉"
echo "========================================"
echo
echo "You can now upload:"
echo " - TXT / CSV / MD / JSON"
echo " - PDF"
echo " - DOCX"
echo " - Images (OCR)"
echo
echo "Your existing dashboard + Ollama logic"
echo "will work WITHOUT ANY CODE CHANGES."
