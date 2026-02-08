<?php
// backend/src/scripts/import_firebase.php
// Imports init.sql data to Firebase Realtime Database via REST API
// Usage: php import_firebase.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$firebaseUrl = getenv('FIREBASE_DB_URL') ?: 'https://garage-5ef1a-default-rtdb.europe-west1.firebasedatabase.app/';
$firebaseUrl = rtrim($firebaseUrl, '/') . '/';

$sqlFile = __DIR__ . '/../../docker/mysql/init.sql';

if (!file_exists($sqlFile)) {
    die("init.sql not found at: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);

// Parse INSERT statements
function parseInsertStatements($sql) {
    $inserts = [];
    $pattern = '/INSERT INTO\s+([`"]?)(\w+)\1\s*\(([^)]+)\)\s*VALUES\s*((?:\([^;]+\))(?:\s*,\s*\([^;]+\))*)\s*;/mi';
    
    if (preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $table = $match[2];
            $cols = array_map('trim', explode(',', preg_replace('/[`"\s]/', '', $match[3])));
            $valuesRaw = $match[4];
            
            // Parse tuples like (val1, val2, ...)
            $tuplePattern = '/\(([^)]+)\)/';
            if (preg_match_all($tuplePattern, $valuesRaw, $tuples)) {
                foreach ($tuples[1] as $tuple) {
                    $values = parseTuple($tuple);
                    $row = [];
                    foreach ($cols as $i => $col) {
                        $row[$col] = $values[$i] ?? null;
                    }
                    $inserts[] = ['table' => $table, 'data' => $row];
                }
            }
        }
    }
    return $inserts;
}

function parseTuple($tuple) {
    $values = [];
    $current = '';
    $inQuote = false;
    $quoteChar = null;
    
    for ($i = 0; $i < strlen($tuple); $i++) {
        $ch = $tuple[$i];
        
        if (!$inQuote && ($ch === '"' || $ch === "'")) {
            $inQuote = true;
            $quoteChar = $ch;
            $current = '';
        } elseif ($inQuote && $ch === $quoteChar) {
            $values[] = $current;
            $inQuote = false;
            $current = '';
            // skip to next comma or end
            while ($i + 1 < strlen($tuple) && $tuple[$i + 1] !== ',') $i++;
        } elseif ($ch === ',' && !$inQuote) {
            $val = trim($current);
            $values[] = $val === 'NULL' ? null : $val;
            $current = '';
        } elseif ($inQuote) {
            $current .= $ch;
        } elseif (!empty($ch) && $ch !== ' ' && $ch !== '\t') {
            $current .= $ch;
        }
    }
    if (!empty($current) || $inQuote) {
        $val = trim($current);
        $values[] = $val === 'NULL' ? null : $val;
    }
    return $values;
}

function putToFirebase($url, $path, $data) {
    $fullUrl = $url . ltrim($path, '/') . '.json';
    $ch = curl_init($fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    return $resp;
}

function postToFirebase($url, $path, $data) {
    $fullUrl = $url . ltrim($path, '/') . '.json';
    $ch = curl_init($fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
}

echo "Parsing init.sql...\n";
$inserts = parseInsertStatements($sql);
echo "Found " . count($inserts) . " data entries.\n\n";

$createdUsers = [];
$tableData = [];

foreach ($inserts as $insert) {
    $table = $insert['table'];
    $data = $insert['data'];
    
    if ($table === 'clients') {
        $uid = $data['firebase_uid'] ?? 'user_' . bin2hex(random_bytes(4));
        $email = $data['email'] ?? '';
        $nom = $data['nom'] ?? '';
        $prenom = $data['prenom'] ?? '';
        
        // Store user info for Auth creation
        $createdUsers[] = [
            'uid' => $uid,
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom,
            'password' => 'Demo@123!',
            'type' => 'client'
        ];
        
        // Write to RTDB under /clients/{uid}
        $userData = ['email' => $email, 'nom' => $nom, 'prenom' => $prenom, 'created_at' => $data['created_at'] ?? date('c')];
        putToFirebase($firebaseUrl, 'clients/' . $uid, $userData);
        echo "✓ Client: $email ($uid)\n";
        
    } elseif ($table === 'admins') {
        $email = $data['email'] ?? '';
        $nom = $data['nom'] ?? '';
        $prenom = $data['prenom'] ?? '';
        $uid = 'admin_' . substr(md5($email), 0, 8);
        
        // Store admin info
        $createdUsers[] = [
            'uid' => $uid,
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom,
            'password' => 'Admin@123!',
            'type' => 'admin'
        ];
        
        // Write to RTDB under /admins/{uid}
        $adminData = ['email' => $email, 'nom' => $nom, 'prenom' => $prenom, 'isAdmin' => true, 'created_at' => date('c')];
        putToFirebase($firebaseUrl, 'admins/' . $uid, $adminData);
        echo "✓ Admin: $email ($uid)\n";
        
    } else {
        // Generic tables: add data with auto ID
        if (!isset($tableData[$table])) $tableData[$table] = [];
        $tableData[$table][] = $data;
    }
}

// Write generic tables
foreach ($tableData as $table => $entries) {
    foreach ($entries as $entry) {
        $resp = postToFirebase($firebaseUrl, $table, $entry);
        echo "✓ $table: " . substr($resp, 0, 50) . "\n";
    }
}

echo "\n=== IMPORT COMPLETE ===\n";
echo "Created users (email/password for manual Auth setup):\n";
foreach ($createdUsers as $user) {
    echo "  [{$user['type']}] {$user['email']} / {$user['password']}\n";
}
echo "\nTo create these users in Firebase Auth:\n";
echo "1. Go to https://console.firebase.google.com/project/garage-5ef1a/authentication/users\n";
echo "2. Click 'Add user' and create each user manually, or\n";
echo "3. Use the Firebase CLI: firebase auth:import users.csv --hash-algo=scrypt --rounds=8 --mem-cost=14\n";

?>
