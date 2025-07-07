<?php

//CLD-937f339f-af9a-40b4-8062-38dd70d7941a
// Start session to store the token
session_start();

// Initialize variables to store messages
$message = "";
$token = "";
$user_email = "";
$email = "";
$user_uuid = "";
$user_role = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get email and password from the form
    $email = $_POST['email'];
    $user_email = $_POST['email'];
    $password = $_POST['password'];
    
    // Prepare the data for the API request
    $data = array(
        'email' => $email,
        'password' => $password
    );
    $data_string = json_encode($data);
    
    // Initialize cURL session
    //$ch = curl_init('http://localhost:8887/api/v1/auth/login');
    
    // Get the base API URL from an environment variable, with a fallback for local development.
    $apiBaseUrl = getenv('AUTH_SERVICE_BASE_URL') ?: 'http://localhost:8887/api/v1';
    // Initialize cURL session
    $ch = curl_init($apiBaseUrl . '/auth/login');
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));
    
    // Execute the request
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL session
    curl_close($ch);
    
    // Process the response
    if ($httpcode == 200) {
        $response = json_decode($result, true);
        if (isset($response['access_token'])) {
            $message = "OK you are logged";
            //el json del API devuelve access_token y grabamos en $token
            $token = $response['access_token'];
            $user_uuid = $response['uuid'];
            $user_role = $response['user_role'];
            
            // Store token in session
            $_SESSION['access_token'] = $token;
            $_SESSION['user_email'] = $email;
            //$_SESSION['user_uuid'] = $response['uuid'];
            $_SESSION['user_uuid'] = $response['uuid'];
            $_SESSION['user_role'] = $response['user_role'];

            // ---->>>>>>>>>>  Redirect to dashboard
            header("Location: consola.php");
            //exit();

        } else {
            $message = "Error: Invalid response from server";
        }
    } else {
        $message = "Error: Authentication failed";
    }
}
?>