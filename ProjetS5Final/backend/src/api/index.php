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
        },
        "/api/auth/register" => function() {
            $data = json_decode(file_get_contents("php://input"), true);
            return [
                "success" => true,
                "message" => "Compte créé avec succès",
                "user" => $data
            ];
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
