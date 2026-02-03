<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Setup-Key");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../firebase_rest.php';

// ============ DEBUG ENDPOINT (view all data) ============
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api/debug/view-data') !== false) {
    $allData = fb_get('') ?: [];
    echo json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// ============ SETUP ENDPOINT (one-time Firebase Auth creation) ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/api/setup/firebase-auth') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $setupKey = $_SERVER['HTTP_X_SETUP_KEY'] ?? $data['setupKey'] ?? null;
    
    if ($setupKey !== 'garage_elite_setup_2025') {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid setup key']);
        exit();
    }
    
    $apiKey = $data['apiKey'] ?? null;
    if (!$apiKey) {
        http_response_code(400);
        echo json_encode(['error' => 'apiKey required']);
        exit();
    }
    
    $clients = fb_get('clients') ?: [];
    $admins = fb_get('admins') ?: [];
    $users = [];
    
    foreach ($clients as $uid => $client) {
        if (!empty($client['email'])) $users[] = ['uid' => $uid, 'email' => $client['email'], 'displayName' => trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')), 'password' => 'Client@123!'];
    }
    foreach ($admins as $uid => $admin) {
        if (!empty($admin['email'])) $users[] = ['uid' => $uid, 'email' => $admin['email'], 'displayName' => trim(($admin['prenom'] ?? '') . ' ' . ($admin['nom'] ?? '')), 'password' => 'Admin@123!'];
    }
    
    $created = [];
    foreach ($users as $user) {
        $ch = curl_init('https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . urlencode($apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $user['email'], 'password' => $user['password'], 'returnSecureToken' => true]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resp, true);
        if ($code === 200 || (isset($result['error']) && strpos($result['error']['message'], 'exists') !== false)) {
            $created[] = $user['email'];
        }
    }
    echo json_encode(['created' => count($created), 'users' => $created]);
    exit();
}

function handleAuthLogin() {
    $data = json_decode(file_get_contents("php://input"), true);

    $clients = fb_get('clients') ?: [];
    foreach ($clients as $key => $c) {
        if (!empty($c['email']) && $c['email'] === ($data['email'] ?? '')) {
            return [
                'success' => true,
                'token' => 'token_' . bin2hex(random_bytes(16)),
                'user' => [
                    'id' => $key,
                    'nom' => $c['nom'] ?? null,
                    'prenom' => $c['prenom'] ?? null,
                    'email' => $c['email'] ?? null
                ]
            ];
        }
    }

    if (($data['email'] ?? '') === 'demo@garage-elite.com' && ($data['password'] ?? '') === 'Demo123!') {
        return [
            'success' => true,
            'token' => 'demo_token_' . time(),
            'user' => [
                'id' => 'demo_user',
                'nom' => 'Demo',
                'prenom' => 'User',
                'email' => 'demo@garage-elite.com'
            ]
        ];
    }

    return ['success' => false, 'message' => 'Identifiants incorrects'];
}

function handleAuthRegister() {
    $data = json_decode(file_get_contents("php://input"), true);

    $clients = fb_get('clients') ?: [];
    foreach ($clients as $c) {
        if (!empty($c['email']) && $c['email'] === ($data['email'] ?? '')) {
            return ['success' => false, 'message' => "Cet email existe déjà"];
        }
    }

    $firebase_uid = 'user_' . bin2hex(random_bytes(8));
    $payload = [
        'firebase_uid' => $firebase_uid,
        'nom' => $data['lastName'] ?? '',
        'prenom' => $data['firstName'] ?? '',
        'email' => $data['email'] ?? '',
        'created_at' => date('c')
    ];

    $res = fb_post('clients', $payload);
    if (isset($res['name'])) {
        return [
            'success' => true,
            'message' => 'Compte créé avec succès',
            'user' => [
                'id' => $res['name'],
                'nom' => $payload['nom'],
                'prenom' => $payload['prenom'],
                'email' => $payload['email']
            ]
        ];
    }

    return ['success' => false, 'message' => 'Erreur lors de la création du compte'];
}

function handleGetClients() {
    $clients = fb_get('clients') ?: [];
    $out = [];
    foreach ($clients as $key => $c) {
        $c['id'] = $key;
        $out[] = $c;
    }
    return $out;
}

function handleGetRepairs() {
    $reps = fb_get('reparations') ?: [];
    $voitures = fb_get('voitures') ?: [];
    $types = fb_get('type_interventions') ?: [];
    $out = [];
    foreach ($reps as $key => $r) {
        $voiture = isset($voitures[$r['voiture_id']]) ? $voitures[$r['voiture_id']] : null;
        $type = isset($types[$r['type_intervention_id']]) ? $types[$r['type_intervention_id']] : null;
        $out[] = [
            'id' => $key,
            'created_at' => $r['created_at'] ?? null,
            'voiture_id' => $r['voiture_id'] ?? null,
            'immatriculation' => $voiture['immatriculation'] ?? null,
            'intervention' => $type['nom'] ?? null,
            'prix' => $type['prix'] ?? null,
            'duree_secondes' => $type['duree_secondes'] ?? null,
            'statut' => $r['statut'] ?? null,
            'in_garage' => isset($r['in_garage']) ? boolval($r['in_garage']) : false,
            'start_time' => $r['start_time'] ?? null,
            'end_time' => $r['end_time'] ?? null,
            'paid' => isset($r['paid']) ? boolval($r['paid']) : false
        ];
    }
    return $out;
}

function handleAddRepair() {
    $data = json_decode(file_get_contents('php://input'), true);

    // find client key by email
    $clients = fb_get('clients') ?: [];
    $clientKey = null;
    if (!empty($data['client_email'])) {
        foreach ($clients as $k => $c) {
            if (!empty($c['email']) && $c['email'] === $data['client_email']) {
                $clientKey = $k;
                break;
            }
        }
    }

    $voitures = fb_get('voitures') ?: [];
    $voitureId = null;
    if ($clientKey) {
        foreach ($voitures as $k => $v) {
            if (!empty($v['client_id']) && $v['client_id'] === $clientKey) {
                $voitureId = $k;
                break;
            }
        }
        if (!$voitureId) {
            $vpay = ['client_id' => $clientKey, 'immatriculation' => $data['immatriculation'] ?? '', 'description' => $data['voiture_desc'] ?? '', 'statut_id' => 1, 'created_at' => date('c')];
            $vres = fb_post('voitures', $vpay);
            if (isset($vres['name'])) $voitureId = $vres['name'];
        }
    }

    // find or create type
    $types = fb_get('type_interventions') ?: [];
    $typeId = null;
    if (!empty($data['intervention'])) {
        foreach ($types as $k => $t) {
            if (!empty($t['nom']) && $t['nom'] === $data['intervention']) { $typeId = $k; break; }
        }
        if (!$typeId) {
            $tpay = ['nom' => $data['intervention'], 'prix' => floatval($data['price'] ?? 0), 'duree_secondes' => intval($data['duration'] ?? 0)];
            $tres = fb_post('type_interventions', $tpay);
            if (isset($tres['name'])) $typeId = $tres['name'];
        }
    }

    if ($voitureId && $typeId) {
        $rpay = ['voiture_id' => $voitureId, 'type_intervention_id' => $typeId, 'statut' => 'Planifiée', 'created_at' => date('c')];
        $rres = fb_post('reparations', $rpay);
        if (isset($rres['name'])) return ['success' => true, 'message' => 'Réparation ajoutée', 'id' => $rres['name']];
    }
    return ['success' => false, 'message' => 'Données manquantes'];
}

// Add to garage (ensure max 2 active in garage)
function handleAddToGarage($repairId) {
    if (!$repairId) return ['success' => false, 'message' => 'Missing repair id'];
    $reps = fb_get('reparations') ?: [];
    $now = time();
    $activeCount = 0;
    foreach ($reps as $k => $r) {
        if (!empty($r['in_garage']) && !empty($r['end_time'])) {
            $end = strtotime($r['end_time']);
            if ($end > $now) $activeCount++;
        } elseif (!empty($r['in_garage']) && empty($r['end_time'])) {
            $activeCount++;
        }
    }
    if ($activeCount >= 2) return ['success' => false, 'message' => 'Garage plein (2 voitures max)'];

    $target = $reps[$repairId] ?? null;
    if (!$target) return ['success' => false, 'message' => 'Réparation introuvable'];

    $target['in_garage'] = true;
    if (empty($target['start_time'])) $target['start_time'] = date('c');
    // if duree known, compute end_time
    $types = fb_get('type_interventions') ?: [];
    $type = $types[$target['type_intervention_id']] ?? null;
    $duree = intval($type['duree_secondes'] ?? 0);
    if ($duree > 0) $target['end_time'] = date('c', strtotime($target['start_time']) + $duree);

    $res = fb_put('reparations/' . $repairId, $target);
    return ['success' => true, 'repair' => $res];
}

// Mark a repair as paid
function handleMarkAsPaid($repairId) {
    if (!$repairId) return ['success' => false, 'message' => 'Missing repair id'];
    $reps = fb_get('reparations') ?: [];
    $target = $reps[$repairId] ?? null;
    if (!$target) return ['success' => false, 'message' => 'Réparation introuvable'];
    
    $target['paid'] = true;
    $res = fb_put('reparations/' . $repairId, $target);
    return ['success' => true, 'repair' => $res];
}

// Remove repair from garage (archive it with recovered flag)
function handleRemoveFromGarage($repairId) {
    if (!$repairId) return ['success' => false, 'message' => 'Missing repair id'];
    $reps = fb_get('reparations') ?: [];
    $target = $reps[$repairId] ?? null;
    if (!$target) return ['success' => false, 'message' => 'Réparation introuvable'];
    
    // Mark as recovered and move to archives
    $target['recovered'] = true;
    $target['recovered_at'] = date('c');
    $target['in_garage'] = false;
    
    // Archive: store in /archives/reparations/<id>
    fb_put('archives/reparations/' . $repairId, $target);
    
    // Delete from active repairs
    fb_put('reparations/' . $repairId, null);
    
    return ['success' => true, 'message' => 'Voiture récupérée et archivée'];
}

// DEBUG endpoint: create test repairs
function handleCreateTestRepairs() {
    $client_email = 'demo@garage-elite.com';
    $clients = fb_get('clients') ?: [];
    $clientKey = null;
    foreach ($clients as $k => $c) {
        if (!empty($c['email']) && $c['email'] === $client_email) {
            $clientKey = $k;
            break;
        }
    }
    if (!$clientKey) return ['success' => false, 'message' => 'Client demo not found'];

    // create or find voitures
    $voitures = fb_get('voitures') ?: [];
    $voitureIds = [];
    $immatriculations = ['AB-123-CD', 'XY-789-ZA', 'CD-456-EF'];
    foreach ($immatriculations as $immat) {
        $found = false;
        foreach ($voitures as $k => $v) {
            if (!empty($v['immatriculation']) && $v['immatriculation'] === $immat) {
                $voitureIds[] = $k;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $vpay = ['client_id' => $clientKey, 'immatriculation' => $immat, 'description' => 'Voiture de test', 'statut_id' => '1', 'created_at' => date('c')];
            $vres = fb_post('voitures', $vpay);
            if (isset($vres['name'])) $voitureIds[] = $vres['name'];
        }
    }

    // create or find types
    $types = fb_get('type_interventions') ?: [];
    $interventionData = [
        ['nom' => 'Freinage', 'prix' => 150, 'duree_secondes' => 7200],
        ['nom' => 'Moteur', 'prix' => 300, 'duree_secondes' => 14400],
        ['nom' => 'Suspension', 'prix' => 200, 'duree_secondes' => 10800]
    ];
    $typeIds = [];
    foreach ($interventionData as $idata) {
        $found = false;
        foreach ($types as $k => $t) {
            if (!empty($t['nom']) && $t['nom'] === $idata['nom']) {
                $typeIds[] = $k;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $tres = fb_post('type_interventions', $idata);
            if (isset($tres['name'])) $typeIds[] = $tres['name'];
        }
    }

    // create repairs
    $reps = fb_get('reparations') ?: [];
    $created = 0;
    if (count($voitureIds) > 0 && count($typeIds) > 0) {
        for ($i = 0; $i < min(3, count($voitureIds)); $i++) {
            $typeIdx = $i % count($typeIds);
            $rpay = ['voiture_id' => $voitureIds[$i], 'type_intervention_id' => $typeIds[$typeIdx], 'statut' => 'Planifiée', 'created_at' => date('c')];
            $rres = fb_post('reparations', $rpay);
            if (isset($rres['name'])) $created++;
        }
    }

    return ['success' => true, 'message' => "Créé $created réparations de test"];
}

function handleGetTypes() {
    $types = fb_get('type_interventions') ?: [];
    $out = [];
    foreach ($types as $k => $t) { $t['id'] = $k; $out[] = $t; }
    return $out;
}

function handleAddType() {
    $data = json_decode(file_get_contents('php://input'), true);
    $nom = $data['nom'] ?? '';
    if (trim($nom) === '') return ['success' => false, 'message' => 'Nom requis'];
    $payload = ['nom' => $nom, 'prix' => isset($data['prix']) ? floatval($data['prix']) : 0, 'duree_secondes' => isset($data['duree_secondes']) ? intval($data['duree_secondes']) : 0];
    $res = fb_post('type_interventions', $payload);
    if (isset($res['name'])) return ['success' => true, 'id' => $res['name']];
    return ['success' => false, 'message' => 'Erreur lors de la création du type'];
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

// Add to garage for a repair
if ($method === "POST" && preg_match('#/api/repairs/([^/]+)/add_to_garage#', $uri, $m)) {
    echo json_encode(handleAddToGarage($m[1]));
    exit();
}

// Mark a repair as paid
if ($method === "POST" && preg_match('#/api/repairs/([^/]+)/mark-paid#', $uri, $m)) {
    echo json_encode(handleMarkAsPaid($m[1]));
    exit();
}

// Remove repair from garage
if ($method === "POST" && preg_match('#/api/repairs/([^/]+)/remove-from-garage#', $uri, $m)) {
    echo json_encode(handleRemoveFromGarage($m[1]));
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

// DEBUG: create test repairs (one-time setup)
if ($method === "GET" && strpos($uri, "/api/setup/create-test-repairs") !== false) {
    echo json_encode(handleCreateTestRepairs());
    exit();
}

// Default 404
http_response_code(404);
echo json_encode(["error" => "Route not found: " . $uri]);
?>

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
=======
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Setup-Key");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../firebase_rest.php';

// ============ DEBUG ENDPOINT (view all data) ============
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api/debug/view-data') !== false) {
    $allData = fb_get('') ?: [];
    echo json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// ============ SETUP ENDPOINT (one-time Firebase Auth creation) ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/api/setup/firebase-auth') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $setupKey = $_SERVER['HTTP_X_SETUP_KEY'] ?? $data['setupKey'] ?? null;
    
    if ($setupKey !== 'garage_elite_setup_2025') {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid setup key']);
        exit();
    }
    
    $apiKey = $data['apiKey'] ?? null;
    if (!$apiKey) {
        http_response_code(400);
        echo json_encode(['error' => 'apiKey required']);
        exit();
    }
    
    $clients = fb_get('clients') ?: [];
    $admins = fb_get('admins') ?: [];
    $users = [];
    
    foreach ($clients as $uid => $client) {
        if (!empty($client['email'])) $users[] = ['uid' => $uid, 'email' => $client['email'], 'displayName' => trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')), 'password' => 'Client@123!'];
    }
    foreach ($admins as $uid => $admin) {
        if (!empty($admin['email'])) $users[] = ['uid' => $uid, 'email' => $admin['email'], 'displayName' => trim(($admin['prenom'] ?? '') . ' ' . ($admin['nom'] ?? '')), 'password' => 'Admin@123!'];
    }
    
    $created = [];
    foreach ($users as $user) {
        $ch = curl_init('https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . urlencode($apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $user['email'], 'password' => $user['password'], 'returnSecureToken' => true]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($resp, true);
        if ($code === 200 || (isset($result['error']) && strpos($result['error']['message'], 'exists') !== false)) {
            $created[] = $user['email'];
        }
    }
    echo json_encode(['created' => count($created), 'users' => $created]);
    exit();
}

function handleAuthLogin() {
    $data = json_decode(file_get_contents("php://input"), true);

    $clients = fb_get('clients') ?: [];
    foreach ($clients as $key => $c) {
        if (!empty($c['email']) && $c['email'] === ($data['email'] ?? '')) {
            return [
                'success' => true,
                'token' => 'token_' . bin2hex(random_bytes(16)),
                'user' => [
                    'id' => $key,
                    'nom' => $c['nom'] ?? null,
                    'prenom' => $c['prenom'] ?? null,
                    'email' => $c['email'] ?? null
                ]
            ];
        }
    }

    if (($data['email'] ?? '') === 'demo@garage-elite.com' && ($data['password'] ?? '') === 'Demo123!') {
        return [
            'success' => true,
            'token' => 'demo_token_' . time(),
            'user' => [
                'id' => 'demo_user',
                'nom' => 'Demo',
                'prenom' => 'User',
                'email' => 'demo@garage-elite.com'
            ]
        ];
    }

    return ['success' => false, 'message' => 'Identifiants incorrects'];
}

function handleAuthRegister() {
    $data = json_decode(file_get_contents("php://input"), true);

    $clients = fb_get('clients') ?: [];
    foreach ($clients as $c) {
        if (!empty($c['email']) && $c['email'] === ($data['email'] ?? '')) {
            return ['success' => false, 'message' => "Cet email existe déjà"];
        }
    }

    $firebase_uid = 'user_' . bin2hex(random_bytes(8));
    $payload = [
        'firebase_uid' => $firebase_uid,
        'nom' => $data['lastName'] ?? '',
        'prenom' => $data['firstName'] ?? '',
        'email' => $data['email'] ?? '',
        'created_at' => date('c')
    ];

    $res = fb_post('clients', $payload);
    if (isset($res['name'])) {
        return [
            'success' => true,
            'message' => 'Compte créé avec succès',
            'user' => [
                'id' => $res['name'],
                'nom' => $payload['nom'],
                'prenom' => $payload['prenom'],
                'email' => $payload['email']
            ]
        ];
    }

    return ['success' => false, 'message' => 'Erreur lors de la création du compte'];
}

function handleGetClients() {
    $clients = fb_get('clients') ?: [];
    $out = [];
    foreach ($clients as $key => $c) {
        $c['id'] = $key;
        $out[] = $c;
    }
    return $out;
}

function handleGetRepairs() {
    $reps = fb_get('reparations') ?: [];
    $voitures = fb_get('voitures') ?: [];
    $types = fb_get('type_interventions') ?: [];
    $out = [];
    foreach ($reps as $key => $r) {
        $voiture = isset($voitures[$r['voiture_id']]) ? $voitures[$r['voiture_id']] : null;
        $type = isset($types[$r['type_intervention_id']]) ? $types[$r['type_intervention_id']] : null;
        $out[] = [
            'id' => $key,
            'created_at' => $r['created_at'] ?? null,
            'voiture_id' => $r['voiture_id'] ?? null,
            'immatriculation' => $voiture['immatriculation'] ?? null,
            'intervention' => $type['nom'] ?? null,
            'prix' => $type['prix'] ?? null,
            'duree_secondes' => $type['duree_secondes'] ?? null,
            'statut' => $r['statut'] ?? null,
            'in_garage' => isset($r['in_garage']) ? boolval($r['in_garage']) : false,
            'start_time' => $r['start_time'] ?? null,
            'end_time' => $r['end_time'] ?? null,
            'paid' => isset($r['paid']) ? boolval($r['paid']) : false
        ];
    }
    return $out;
}

function handleAddRepair() {
    $data = json_decode(file_get_contents('php://input'), true);

    // find client key by email
    $clients = fb_get('clients') ?: [];
    $clientKey = null;
    if (!empty($data['client_email'])) {
        foreach ($clients as $k => $c) {
            if (!empty($c['email']) && $c['email'] === $data['client_email']) {
                $clientKey = $k;
                break;
            }
        }
    }

    $voitures = fb_get('voitures') ?: [];
    $voitureId = null;
    if ($clientKey) {
        foreach ($voitures as $k => $v) {
            if (!empty($v['client_id']) && $v['client_id'] === $clientKey) {
                $voitureId = $k;
                break;
            }
        }
        if (!$voitureId) {
            $vpay = ['client_id' => $clientKey, 'immatriculation' => $data['immatriculation'] ?? '', 'description' => $data['voiture_desc'] ?? '', 'statut_id' => 1, 'created_at' => date('c')];
            $vres = fb_post('voitures', $vpay);
            if (isset($vres['name'])) $voitureId = $vres['name'];
        }
    }

    // find or create type
    $types = fb_get('type_interventions') ?: [];
    $typeId = null;
    if (!empty($data['intervention'])) {
        foreach ($types as $k => $t) {
            if (!empty($t['nom']) && $t['nom'] === $data['intervention']) { $typeId = $k; break; }
        }
        if (!$typeId) {
            $tpay = ['nom' => $data['intervention'], 'prix' => floatval($data['price'] ?? 0), 'duree_secondes' => intval($data['duration'] ?? 0)];
            $tres = fb_post('type_interventions', $tpay);
            if (isset($tres['name'])) $typeId = $tres['name'];
        }
    }

    if ($voitureId && $typeId) {
        $rpay = ['voiture_id' => $voitureId, 'type_intervention_id' => $typeId, 'statut' => 'Planifiée', 'created_at' => date('c')];
        $rres = fb_post('reparations', $rpay);
        if (isset($rres['name'])) return ['success' => true, 'message' => 'Réparation ajoutée', 'id' => $rres['name']];
    }
    return ['success' => false, 'message' => 'Données manquantes'];
}

// Add to garage (ensure max 2 active in garage)
function handleAddToGarage($repairId) {
    if (!$repairId) return ['success' => false, 'message' => 'Missing repair id'];
    $reps = fb_get('reparations') ?: [];
    $now = time();
    $activeCount = 0;
    foreach ($reps as $k => $r) {
        if (!empty($r['in_garage']) && !empty($r['end_time'])) {
            $end = strtotime($r['end_time']);
            if ($end > $now) $activeCount++;
        } elseif (!empty($r['in_garage']) && empty($r['end_time'])) {
            $activeCount++;
        }
    }
    if ($activeCount >= 2) return ['success' => false, 'message' => 'Garage plein (2 voitures max)'];

    $target = $reps[$repairId] ?? null;
    if (!$target) return ['success' => false, 'message' => 'Réparation introuvable'];

    $target['in_garage'] = true;
    if (empty($target['start_time'])) $target['start_time'] = date('c');
    // if duree known, compute end_time
    $types = fb_get('type_interventions') ?: [];
    $type = $types[$target['type_intervention_id']] ?? null;
    $duree = intval($type['duree_secondes'] ?? 0);
    if ($duree > 0) $target['end_time'] = date('c', strtotime($target['start_time']) + $duree);

    $res = fb_put('reparations/' . $repairId, $target);
    return ['success' => true, 'repair' => $res];
}

// Mark a repair as paid
function handleMarkAsPaid($repairId) {
    if (!$repairId) return ['success' => false, 'message' => 'Missing repair id'];
    $reps = fb_get('reparations') ?: [];
    $target = $reps[$repairId] ?? null;
    if (!$target) return ['success' => false, 'message' => 'Réparation introuvable'];
    
    $target['paid'] = true;
    $res = fb_put('reparations/' . $repairId, $target);
    return ['success' => true, 'repair' => $res];
}

// Remove repair from garage (after finished + paid)
function handleRemoveFromGarage($repairId) {
    if (!$repairId) return ['success' => false, 'message' => 'Missing repair id'];
    $reps = fb_get('reparations') ?: [];
    $target = $reps[$repairId] ?? null;
    if (!$target) return ['success' => false, 'message' => 'Réparation introuvable'];
    
    // Delete the repair (voiture leaves)
    fb_put('reparations/' . $repairId, null);
    return ['success' => true, 'message' => 'Voiture récupérée et supprimée'];
}

// DEBUG endpoint: create test repairs
function handleCreateTestRepairs() {
    $client_email = 'demo@garage-elite.com';
    $clients = fb_get('clients') ?: [];
    $clientKey = null;
    foreach ($clients as $k => $c) {
        if (!empty($c['email']) && $c['email'] === $client_email) {
            $clientKey = $k;
            break;
        }
    }
    if (!$clientKey) return ['success' => false, 'message' => 'Client demo not found'];

    // create or find voitures
    $voitures = fb_get('voitures') ?: [];
    $voitureIds = [];
    $immatriculations = ['AB-123-CD', 'XY-789-ZA', 'CD-456-EF'];
    foreach ($immatriculations as $immat) {
        $found = false;
        foreach ($voitures as $k => $v) {
            if (!empty($v['immatriculation']) && $v['immatriculation'] === $immat) {
                $voitureIds[] = $k;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $vpay = ['client_id' => $clientKey, 'immatriculation' => $immat, 'description' => 'Voiture de test', 'statut_id' => '1', 'created_at' => date('c')];
            $vres = fb_post('voitures', $vpay);
            if (isset($vres['name'])) $voitureIds[] = $vres['name'];
        }
    }

    // create or find types
    $types = fb_get('type_interventions') ?: [];
    $interventionData = [
        ['nom' => 'Freinage', 'prix' => 150, 'duree_secondes' => 7200],
        ['nom' => 'Moteur', 'prix' => 300, 'duree_secondes' => 14400],
        ['nom' => 'Suspension', 'prix' => 200, 'duree_secondes' => 10800]
    ];
    $typeIds = [];
    foreach ($interventionData as $idata) {
        $found = false;
        foreach ($types as $k => $t) {
            if (!empty($t['nom']) && $t['nom'] === $idata['nom']) {
                $typeIds[] = $k;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $tres = fb_post('type_interventions', $idata);
            if (isset($tres['name'])) $typeIds[] = $tres['name'];
        }
    }

    // create repairs
    $reps = fb_get('reparations') ?: [];
    $created = 0;
    if (count($voitureIds) > 0 && count($typeIds) > 0) {
        for ($i = 0; $i < min(3, count($voitureIds)); $i++) {
            $typeIdx = $i % count($typeIds);
            $rpay = ['voiture_id' => $voitureIds[$i], 'type_intervention_id' => $typeIds[$typeIdx], 'statut' => 'Planifiée', 'created_at' => date('c')];
            $rres = fb_post('reparations', $rpay);
            if (isset($rres['name'])) $created++;
        }
    }

    return ['success' => true, 'message' => "Créé $created réparations de test"];
}

function handleGetTypes() {
    $types = fb_get('type_interventions') ?: [];
    $out = [];
    foreach ($types as $k => $t) { $t['id'] = $k; $out[] = $t; }
    return $out;
}

function handleAddType() {
    $data = json_decode(file_get_contents('php://input'), true);
    $nom = $data['nom'] ?? '';
    if (trim($nom) === '') return ['success' => false, 'message' => 'Nom requis'];
    $payload = ['nom' => $nom, 'prix' => isset($data['prix']) ? floatval($data['prix']) : 0, 'duree_secondes' => isset($data['duree_secondes']) ? intval($data['duree_secondes']) : 0];
    $res = fb_post('type_interventions', $payload);
    if (isset($res['name'])) return ['success' => true, 'id' => $res['name']];
    return ['success' => false, 'message' => 'Erreur lors de la création du type'];
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

// Add to garage for a repair
if ($method === "POST" && preg_match('#/api/repairs/([^/]+)/add_to_garage#', $uri, $m)) {
    echo json_encode(handleAddToGarage($m[1]));
    exit();
}

// Mark a repair as paid
if ($method === "POST" && preg_match('#/api/repairs/([^/]+)/mark-paid#', $uri, $m)) {
    echo json_encode(handleMarkAsPaid($m[1]));
    exit();
}

// Remove repair from garage
if ($method === "POST" && preg_match('#/api/repairs/([^/]+)/remove-from-garage#', $uri, $m)) {
    echo json_encode(handleRemoveFromGarage($m[1]));
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

// DEBUG: create test repairs (one-time setup)
if ($method === "GET" && strpos($uri, "/api/setup/create-test-repairs") !== false) {
    echo json_encode(handleCreateTestRepairs());
    exit();
}

// Default 404
http_response_code(404);
echo json_encode(["error" => "Route not found: " . $uri]);
?>

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
>>>>>>> b79b6626 (Add files via upload)
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
>>>>>>> 0708d5c7 (Add files via upload)
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
