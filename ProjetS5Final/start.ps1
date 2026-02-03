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
Write-Host "✅ Garage Elite est prêt !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "🌐 Frontend React: http://localhost:3000" -ForegroundColor White
Write-Host "🔧 Backend API:    http://localhost:8000" -ForegroundColor White
Write-Host "📊 PHPMyAdmin:     http://localhost:8080" -ForegroundColor White
Write-Host "🗄️  MySQL:         localhost:3307" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Green