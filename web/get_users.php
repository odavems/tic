<?php  

//ESTE ARCHIVO CREA UN JSON CON LOS DATOS 
//DE USUARIOS: NOMBRES Y UUIDS
//CON ESTOS DATOS SE HACE EN EL ARCHIVO consola.php
//EL FETCH CON LA FUNCION fetchUserName($uuid) 
//PARA PRESENTAR EL NOMBRE EN LUGAR DEL UUID
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = "";
    $token = $_SESSION['access_token'];

    // API endpoint for fetching users
    $apiUrl = "http://localhost:8887/api/v1/auth/users";
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json')       
    );
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("API request failed: $error");
    }
    
    //$error_message = "No hay token o no se pudo conectar a la DB. Error: Failed to fetch tickets (HTTP Code: $httpcode)";

    // Close cURL session
    curl_close($ch);

    // Parse the JSON response
    $users = json_decode($response, true);
    $_SESSION['users'] = $users;

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to parse API response: " . json_last_error_msg());
    }

    $users_names_and_uuids = [];
    foreach ($users as $user) {
        if (isset($user['uuid']) && isset($user['name'])) {
            $users_names_and_uuids[] = [
                'uuid' => $user['uuid'],
                'name' => $user['name'],
                'email' => $user['email'],
            ];
        }
    }

    $_SESSION['users_names_and_uuids'] = $users_names_and_uuids;

    // echo "<h1>get_users.php array only names and uuids</h1>";
    // echo "<pre>";
    // print_r($users_names_and_uuids);
    // echo "</pre>";

   //echo "<script>console.log('users: " . json_encode($users_names_and_uuids) . "');</script>"; 
    echo "<script>console.log('get_users.php  NAMES and UUIDS:', " . json_encode($users_names_and_uuids, JSON_PRETTY_PRINT) . ");</script>";

?>



