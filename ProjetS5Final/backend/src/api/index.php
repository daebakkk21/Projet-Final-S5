<?php
// backend/src/api/index.php - API endpoints using MySQL Database
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../db.php';

function route_auth_login() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['email']) || empty($data['password'])) {
        return ['success' => false, 'message' => 'Email et mot de passe requis'];
    }

    $pdo = getDB();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        // Chercher le client dans la table clients
        $stmt = $pdo->prepare('SELECT id, firebase_uid, nom, prenom, email FROM clients WHERE email = ?');
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Pour les clients, pas de vérification de mot de passe (connexion Firebase)
            $token = 'token_' . bin2hex(random_bytes(16));
            return [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'firebase_uid' => $user['firebase_uid'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email' => $user['email'],
                    'role' => 'client'
                ]
            ];
        }

        // Chercher dans la table admins
        $stmt = $pdo->prepare('SELECT id, nom, prenom, email, mdp FROM admins WHERE email = ?');
        $stmt->execute([$data['email']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($data['password'], $admin['mdp'])) {
            $token = 'admin_token_' . bin2hex(random_bytes(16));
            return [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $admin['id'],
                    'nom' => $admin['nom'],
                    'prenom' => $admin['prenom'],
                    'email' => $admin['email'],
                    'role' => 'admin'
                ]
            ];
        }

        return ['success' => false, 'message' => 'Identifiants incorrects'];
    } catch (Exception $e) {
        error_log('Erreur login: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur serveur'];
    }
}

function route_auth_register() {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['email']) || empty($data['firstName']) || empty($data['lastName'])) {
        return ['success' => false, 'message' => 'Tous les champs sont requis'];
    }

    $pdo = getDB();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Erreur de connexion à la base de données'];
    }

    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id FROM clients WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }

        // Générer un UID Firebase unique
        $firebase_uid = 'user_' . bin2hex(random_bytes(8));

        // Insérer le nouveau client
        $stmt = $pdo->prepare('
            INSERT INTO clients (firebase_uid, nom, prenom, email, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ');
        
        if ($stmt->execute([$firebase_uid, $data['lastName'], $data['firstName'], $data['email']])) {
            $client_id = $pdo->lastInsertId();
            return [
                'success' => true,
                'message' => 'Compte créé avec succès',
                'user' => [
                    'id' => $client_id,
                    'firebase_uid' => $firebase_uid,
                    'nom' => $data['lastName'],
                    'prenom' => $data['firstName'],
                    'email' => $data['email'],
                    'role' => 'client'
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de la création du compte'];
    } catch (Exception $e) {
        error_log('Erreur register: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Erreur serveur'];
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if ($method === 'POST' && strpos($uri, '/api/auth/login') !== false) {
    echo json_encode(route_auth_login()); exit();
}
if ($method === 'POST' && strpos($uri, '/api/auth/register') !== false) {
    echo json_encode(route_auth_register()); exit();
}
if ($method === 'GET' && strpos($uri, '/api') !== false) {
    echo json_encode(['message' => 'Garage Elite API v1.0']); exit();
}

http_response_code(404);
echo json_encode(['error' => 'Route not found: ' . $uri]);

?>
