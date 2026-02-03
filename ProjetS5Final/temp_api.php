<<<<<<< HEAD
<?php
// temp_api.php — thin proxy to the cleaned public API (uses Firebase Realtime DB)
require_once __DIR__ . '/backend/src/public/api/index.php';
exit();

=======
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

function getDBConnection() {
    $host = getenv('DB_HOST') ?: 'mysql';
    $db = getenv('DB_DATABASE') ?: 'garage_elite';
    $user = getenv('DB_USERNAME') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: 'root';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

function handleAuthLogin() {
    $data = json_decode(file_get_contents("php://input"), true);
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return ["success" => false, "message" => "Erreur de connexion à la base de données"];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $data["email"]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            return [
                "success" => true,
                "token" => "token_" . bin2hex(random_bytes(16)),
                "user" => [
                    "id" => $user["id"],
                    "nom" => $user["nom"],
                    "prenom" => $user["prenom"],
                    "email" => $user["email"]
                ]
            ];
        }
        
        if ($data["email"] === "demo@garage-elite.com" && $data["password"] === "Demo123!") {
            return [
                "success" => true,
                "token" => "demo_token_" . time(),
                "user" => [
                    "id" => 1,
                    "nom" => "Demo",
                    "prenom" => "User",
                    "email" => "demo@garage-elite.com"
                ]
            ];
        }
        
        return ["success" => false, "message" => "Identifiants incorrects"];
    } catch (Exception $e) {
        return ["success" => false, "message" => "Erreur: " . $e->getMessage()];
    }
}

function handleAuthRegister() {
    $data = json_decode(file_get_contents("php://input"), true);
    $pdo = getDBConnection();
    
    if (!$pdo) {
        return ["success" => false, "message" => "Erreur de connexion à la base de données"];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $data["email"]]);
        
        if ($stmt->fetch()) {
            return ["success" => false, "message" => "Cet email existe déjà"];
        }
        
        $firebase_uid = "user_" . bin2hex(random_bytes(8));
        $stmt = $pdo->prepare("
            INSERT INTO clients (firebase_uid, nom, prenom, email)
            VALUES (:firebase_uid, :nom, :prenom, :email)
        ");
        
        $stmt->execute([
            ':firebase_uid' => $firebase_uid,
            ':nom' => $data["lastName"] ?? '',
            ':prenom' => $data["firstName"] ?? '',
            ':email' => $data["email"]
        ]);
        
        $user_id = $pdo->lastInsertId();
        
        return [
            "success" => true,
            "message" => "Compte créé avec succès",
            "user" => [
                "id" => $user_id,
                "nom" => $data["lastName"],
                "prenom" => $data["firstName"],
                "email" => $data["email"]
            ]
        ];
    } catch (Exception $e) {
        return ["success" => false, "message" => "Erreur: " . $e->getMessage()];
    }
}

// Route dispatcher
$method = $_SERVER["REQUEST_METHOD"];
$uri = $_SERVER["REQUEST_URI"];

// Check for login
if ($method === "POST" && strpos($uri, "/api/auth/login") !== false) {
    echo json_encode(handleAuthLogin());
    exit();
}

// Check for register
if ($method === "POST" && strpos($uri, "/api/auth/register") !== false) {
    echo json_encode(handleAuthRegister());
    exit();
}

// Check for API root
if ($method === "GET" && strpos($uri, "/api/index.php") !== false) {
    echo json_encode(["message" => "Garage Elite API v1.0"]);
    exit();
}

// Default 404
http_response_code(404);
echo json_encode(["error" => "Route not found: " . $uri]);
?>
>>>>>>> 00c61219 (Add files via upload)
