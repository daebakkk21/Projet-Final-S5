<?php
// backend/src/index.php - sert le front statique si présent
$public = __DIR__ . '/public/index.html';
if (file_exists($public)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($public);
    exit;
}

// Sinon message par défaut
echo "<h1>🚗 Garage Elite - Backend</h1>";
echo "<p>API: <a href='/api'>/api</a></p>";
<<<<<<< HEAD
echo "<p>Realtime DB: " . (getenv('FIREBASE_DB_URL') ? htmlentities(getenv('FIREBASE_DB_URL')) : 'not configured') . "</p>";
=======
echo "<p>MySQL: " . (class_exists('PDO') ? '✅' : '❌') . "</p>";
>>>>>>> 1ccc550b (Add files via upload)
