<?php
// backend/src/scripts/setup_reference_data.php
// Manually sets up reference data (statuses) in Firebase Realtime DB
// Usage: php setup_reference_data.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$firebaseUrl = getenv('FIREBASE_DB_URL') ?: 'https://garage-5ef1a-default-rtdb.europe-west1.firebasedatabase.app/';
$firebaseUrl = rtrim($firebaseUrl, '/') . '/';

function putToFirebase($url, $path, $data) {
    $fullUrl = $url . ltrim($path, '/') . '.json';
    $ch = curl_init($fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
}

echo "Setting up reference data (statuses and types)...\n\n";

// Statut Voitures
$statut_voitures = [
    'statut_1' => ['id' => 1, 'nom' => 'En attente', 'description' => 'La voiture est en attente de réparation'],
    'statut_2' => ['id' => 2, 'nom' => 'En réparation', 'description' => 'La voiture est actuellement en réparation'],
    'statut_3' => ['id' => 3, 'nom' => 'Prête', 'description' => 'La voiture est prête à être récupérée']
];
putToFirebase($firebaseUrl, 'statut_voitures', $statut_voitures);
echo "✓ statut_voitures configured\n";

// Statut Reparations
$statut_reparations = [
    'statut_1' => ['id' => 1, 'nom' => 'Planifiée', 'description' => 'La réparation est planifiée'],
    'statut_2' => ['id' => 2, 'nom' => 'En cours', 'description' => 'La réparation est en cours'],
    'statut_3' => ['id' => 3, 'nom' => 'Terminée', 'description' => 'La réparation est terminée']
];
putToFirebase($firebaseUrl, 'statut_reparations', $statut_reparations);
echo "✓ statut_reparations configured\n";

// Statut Paiements
$statut_paiements = [
    'statut_1' => ['id' => 1, 'nom' => 'En attente', 'description' => 'Le paiement est en attente'],
    'statut_2' => ['id' => 2, 'nom' => 'Complété', 'description' => 'Le paiement a été effectué'],
    'statut_3' => ['id' => 3, 'nom' => 'Remboursé', 'description' => 'Le paiement a été remboursé']
];
putToFirebase($firebaseUrl, 'statut_paiements', $statut_paiements);
echo "✓ statut_paiements configured\n";

echo "\n✓ Reference data setup complete!\n";

?>
