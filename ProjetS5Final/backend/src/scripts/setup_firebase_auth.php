<?php
// backend/src/scripts/setup_firebase_auth.php
// Creates Firebase Auth users from Realtime Database client data
// Usage: php setup_firebase_auth.php <api_key>

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($argc < 2) {
    // Try to find API key from environment or file
    $apiKey = getenv('FIREBASE_API_KEY');
    if (!$apiKey) {
        die("Usage: php setup_firebase_auth.php <firebase_api_key>\n" .
            "Or set FIREBASE_API_KEY environment variable\n\n" .
            "Get API Key from: https://console.firebase.google.com/project/garage-5ef1a/settings/general\n");
    }
} else {
    $apiKey = $argv[1];
}

$firebaseUrl = getenv('FIREBASE_DB_URL') ?: 'https://garage-5ef1a-default-rtdb.europe-west1.firebasedatabase.app/';
$firebaseUrl = rtrim($firebaseUrl, '/') . '/';

// Get clients and admins from Realtime DB
function getClientsFromDb($url) {
    $ch = curl_init($url . 'clients.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true) ?: [];
}

function getAdminsFromDb($url) {
    $ch = curl_init($url . 'admins.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true) ?: [];
}

function createFirebaseAuthUser($apiKey, $email, $password, $displayName = '', $uid = null) {
    $url = 'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . urlencode($apiKey);
    
    $data = [
        'email' => $email,
        'password' => $password,
        'returnSecureToken' => true
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($resp, true);
    
    if ($code === 200 && isset($result['localId'])) {
        return ['success' => true, 'uid' => $result['localId'], 'email' => $email];
    } else {
        $err = $result['error']['message'] ?? 'Unknown error';
        return ['success' => false, 'email' => $email, 'error' => $err];
    }
}

echo "=== Firebase Auth Setup ===\n\n";

echo "Fetching clients and admins from Realtime DB...\n";
$clients = getClientsFromDb($firebaseUrl);
$admins = getAdminsFromDb($firebaseUrl);

$users = [];
foreach ($clients as $uid => $client) {
    if (!empty($client['email'])) {
        $users[] = [
            'uid' => $uid,
            'email' => $client['email'],
            'displayName' => trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')),
            'password' => 'Client@123!',
            'type' => 'client'
        ];
    }
}

foreach ($admins as $uid => $admin) {
    if (!empty($admin['email'])) {
        $users[] = [
            'uid' => $uid,
            'email' => $admin['email'],
            'displayName' => trim(($admin['prenom'] ?? '') . ' ' . ($admin['nom'] ?? '')),
            'password' => 'Admin@123!',
            'type' => 'admin'
        ];
    }
}

if (empty($users)) {
    echo "No users found in Realtime DB!\n";
    exit(1);
}

echo "Found " . count($users) . " users to create in Auth.\n";
echo "Creating Firebase Auth users...\n\n";

$created = [];
$failed = [];

foreach ($users as $user) {
    echo "Creating {$user['type']}: {$user['email']}...";
    $result = createFirebaseAuthUser($apiKey, $user['email'], $user['password'], $user['displayName'], $user['uid']);
    
    if ($result['success']) {
        echo " ✓ (UID: {$result['uid']})\n";
        $created[] = $user;
    } else {
        echo " ✗ {$result['error']}\n";
        $failed[] = ['user' => $user, 'error' => $result['error']];
    }
}

echo "\n=== Summary ===\n";
echo "Created: " . count($created) . "\n";
echo "Failed: " . count($failed) . "\n";

if (!empty($failed)) {
    echo "\nFailed users:\n";
    foreach ($failed as $f) {
        echo "  - {$f['user']['email']}: {$f['error']}\n";
    }
}

?>
