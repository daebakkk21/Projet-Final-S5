@echo off
REM Garage Elite - Quick Start Script for Windows
REM Starts Docker containers and tests Firebase API

echo ============================================
echo Garage Elite - Firebase Edition
echo ============================================
echo.

echo Step 1: Starting Docker containers...
echo (MySQL is no longer used - Firebase only^)
docker-compose up -d backend frontend

echo Waiting for backend to start (30 seconds)...
timeout /t 30 /nobreak

echo.
echo ============================================
echo Testing API endpoints...
echo ============================================
echo.

echo Test 1: API Health Check
curl -s http://localhost:8000/api | findstr /R "message"
echo.

echo Test 2: Fetching clients
curl -s http://localhost:8000/api/clients | findstr /R "email"
echo.

echo Test 3: Fetching type_interventions
curl -s http://localhost:8000/api/type_interventions | findstr /R "nom"
echo.

echo ============================================
echo IMPORTANT: Next Steps
echo ============================================
echo.
echo 1. Get your Firebase API Key from:
echo    https://console.firebase.google.com/project/garage-5ef1a/settings/general
echo.
echo 2. Create Firebase Auth users by running:
echo    curl -X POST "http://localhost:8000/api/setup/firebase-auth" ^
echo      -H "Content-Type: application/json" ^
echo      -H "X-Setup-Key: garage_elite_setup_2025" ^
echo      -d "{\"apiKey\": \"YOUR_FIREBASE_API_KEY_HERE\"}"
echo.
echo 3. Test login with:
echo    curl -X POST "http://localhost:8000/api/auth/login" ^
echo      -H "Content-Type: application/json" ^
echo      -d "{\"email\": \"demo@garage-elite.com\", \"password\": \"Demo@123!\"}"
echo.
echo Backend: http://localhost:8000
echo Frontend: http://localhost:3000
echo.
echo See FIREBASE_MIGRATION.md for complete instructions
echo.
