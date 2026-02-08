<?php
// backend/src/firebase_rest.php
// Simple Firebase Realtime Database REST helper using cURL

function fb_get_base_url() {
    return rtrim(getenv('FIREBASE_DB_URL') ?: 'https://garage-5ef1a-default-rtdb.europe-west1.firebasedatabase.app/', '/') . '/';
}

function fb_auth_query() {
    $secret = getenv('FIREBASE_DB_SECRET') ?: getenv('FIREBASE_DATABASE_SECRET');
    return $secret ? ('?auth=' . $secret) : '';
}

function fb_request($method, $path, $data = null) {
    $url = fb_get_base_url() . ltrim($path, '/');
    // ensure .json suffix
    if (substr($url, -5) !== '.json') $url .= '.json';
    $url .= fb_auth_query();

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data !== null) {
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) return null;
    $decoded = json_decode($resp, true);
    return $decoded;
}

function fb_get($path) {
    return fb_request('GET', $path, null);
}

function fb_put($path, $data) {
    return fb_request('PUT', $path, $data);
}

function fb_post($path, $data) {
    return fb_request('POST', $path, $data);
}

function fb_patch($path, $data) {
    return fb_request('PATCH', $path, $data);
}

function fb_delete($path) {
    return fb_request('DELETE', $path, null);
}

?>
