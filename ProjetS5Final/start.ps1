Write-Host "🚗 Démarrage de Garage Elite..." -ForegroundColor Cyan

# Arrêter tout
docker-compose down

# Construire et démarrer
docker-compose up -d --build

Write-Host "⏳ Attente (30 secondes)..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Vérifier
docker ps


Write-Host "========================================" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "Votre projet run sur http://localhost:3000" -ForegroundColor Green