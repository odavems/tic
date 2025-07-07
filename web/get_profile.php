<?php

session_start();

// Check if token exists in session, POST or GET
//$accessToken = $_SESSION['access_token'] ?? "";
//-----------------------------

//g-c472e587cb757688

// Enable CORS if your frontend is on a different domain/port
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET");
// header("Access-Control-Allow-Headers: Content-Type");
// header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $uuid = $_GET['uuid'] ?? null;
    $accessToken = $_GET['accessToken'] ?? null; // You might need to pass an access token

    if ($uuid) {
        $api_url = "http://auth-serv/api/users/" . $uuid;
        //$api_url = "http://localhost:8887/api/v1/auth/users/" . $uuid;
        
        $headers = [];
        if ($accessToken) {
            $headers[] = "Authorization: Bearer " . $accessToken; // Example authorization header
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $headers,
            ],
        ]);

        $response = @file_get_contents($api_url, false, $context);

        if ($response !== false) {
            echo $response; // Send the API response directly back
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Failed to fetch data from the API.']);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'UUID is required.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only GET requests are allowed.']);
}
?>