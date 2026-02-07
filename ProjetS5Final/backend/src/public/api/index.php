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

function handleGetClients() {
    $pdo = getDBConnection();
    if (!$pdo) return ["clients" => []];
    try {
        $stmt = $pdo->query("SELECT id, firebase_uid, nom, prenom, email, created_at FROM clients ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    } catch (Exception $e) {
        return [];
    }
}

function handleGetRepairs() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    try {
        $sql = "SELECT r.id, r.created_at, r.voiture_id, v.immatriculation, t.nom AS intervention, t.prix, t.duree_secondes, s.nom AS statut
                FROM reparations r
                LEFT JOIN voitures v ON r.voiture_id = v.id
                LEFT JOIN type_interventions t ON r.type_intervention_id = t.id
                LEFT JOIN statut_reparations s ON r.statut_id = s.id
                ORDER BY r.created_at DESC";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    } catch (Exception $e) {
        return [];
    }
}

function handleAddRepair() {
    $data = json_decode(file_get_contents('php://input'), true);
    $pdo = getDBConnection();
    if (!$pdo) return ["success" => false, "message" => "DB error"];

    try {
        // find client
        $clientId = null;
        if (!empty($data['client_email'])) {
            $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $data['client_email']]);
            $c = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($c) $clientId = $c['id'];
        }

        // if no voiture exists for client, create a simple voiture record
        $voitureId = null;
        if ($clientId) {
            // try find voiture
            $stmt = $pdo->prepare("SELECT id FROM voitures WHERE client_id = :cid LIMIT 1");
            $stmt->execute([':cid' => $clientId]);
            $v = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($v) {
                $voitureId = $v['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO voitures (client_id, immatriculation, description, statut_id) VALUES (:cid, :immat, :desc, 1)");
                $stmt->execute([':cid' => $clientId, ':immat' => ($data['immatriculation'] ?? ''), ':desc' => ($data['voiture_desc'] ?? '')]);
                $voitureId = $pdo->lastInsertId();
            }
        }

        // ensure type_intervention exists
        $typeId = null;
        if (!empty($data['intervention'])) {
            $stmt = $pdo->prepare("SELECT id FROM type_interventions WHERE nom = :nom LIMIT 1");
            $stmt->execute([':nom' => $data['intervention']]);
            $t = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($t) {
                $typeId = $t['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO type_interventions (nom, prix, duree_secondes) VALUES (:nom, :prix, :duree)");
                $stmt->execute([':nom' => $data['intervention'], ':prix' => ($data['price'] ?? 0), ':duree' => ($data['duration'] ?? 0)]);
                $typeId = $pdo->lastInsertId();
            }
        }

        // insert into reparations if we have required refs
        if ($voitureId && $typeId) {
            $stmt = $pdo->prepare("INSERT INTO reparations (voiture_id, type_intervention_id, statut_id) VALUES (:voiture, :type, 1)");
            $stmt->execute([':voiture' => $voitureId, ':type' => $typeId]);
            return ["success" => true, "message" => "Réparation ajoutée", "id" => $pdo->lastInsertId()];
        }

        return ["success" => false, "message" => "Données insuffisantes pour créer la réparation"];
    } catch (Exception $e) {
        return ["success" => false, "message" => $e->getMessage()];
    }
}

function handleGetTypes() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    try {
        $stmt = $pdo->query("SELECT id, nom, prix, duree_secondes FROM type_interventions ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function handleAddType() {
    $data = json_decode(file_get_contents('php://input'), true);
    $pdo = getDBConnection();
    if (!$pdo) return ["success" => false, "message" => "DB error"];
    try {
        $nom = $data['nom'] ?? '';
        $prix = isset($data['prix']) ? floatval($data['prix']) : 0;
        $duree = isset($data['duree_secondes']) ? intval($data['duree_secondes']) : 0;

        if (trim($nom) === '') return ["success" => false, "message" => "Nom requis"];

        $stmt = $pdo->prepare("INSERT INTO type_interventions (nom, prix, duree_secondes) VALUES (:nom, :prix, :duree)");
        $stmt->execute([':nom' => $nom, ':prix' => $prix, ':duree' => $duree]);
        return ["success" => true, "id" => $pdo->lastInsertId()];
    } catch (Exception $e) {
        return ["success" => false, "message" => $e->getMessage()];
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

// Clients list
if ($method === "GET" && strpos($uri, "/api/clients") !== false) {
    echo json_encode(handleGetClients());
    exit();
}

// Repairs list
if ($method === "GET" && strpos($uri, "/api/repairs") !== false) {
    echo json_encode(handleGetRepairs());
    exit();
}

// Add repair
if ($method === "POST" && strpos($uri, "/api/repairs") !== false) {
    echo json_encode(handleAddRepair());
    exit();
}

// Type interventions list
if ($method === "GET" && strpos($uri, "/api/type_interventions") !== false) {
    echo json_encode(handleGetTypes());
    exit();
}

// Add type intervention
if ($method === "POST" && strpos($uri, "/api/type_interventions") !== false) {
    echo json_encode(handleAddType());
    exit();
}

// Default 404
http_response_code(404);
echo json_encode(["error" => "Route not found: " . $uri]);
?>
