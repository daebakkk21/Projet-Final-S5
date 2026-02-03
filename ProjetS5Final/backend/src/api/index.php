<?php
// backend/src/api/index.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Connexion à MySQL
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

$routes = [
    "GET" => [
        "/api" => function() {
            return ["message" => "Garage Elite API v1.0"];
        },
        "/api/auth/check" => function() {
            return ["authenticated" => false, "user" => null];
        }
    ],
    "POST" => [
        "/api/auth/login" => function() {
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
                
                // Demo account
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
        },
        "/api/auth/register" => function() {
            $data = json_decode(file_get_contents("php://input"), true);
            $pdo = getDBConnection();
            
            if (!$pdo) {
                return ["success" => false, "message" => "Erreur de connexion à la base de données"];
            }
            
            try {
                // Vérifier si email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = :email LIMIT 1");
                $stmt->execute([':email' => $data["email"]]);
                
                if ($stmt->fetch()) {
                    return ["success" => false, "message" => "Cet email existe déjà"];
                }
                
                // Insérer le nouveau client
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
    ]
];

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$method = $_SERVER["REQUEST_METHOD"];

if (isset($routes[$method][$path])) {
    echo json_encode($routes[$method][$path]());
} else {
    http_response_code(404);
    echo json_encode(["error" => "Route not found"]);
}
