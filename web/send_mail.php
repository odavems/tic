<?php

//if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// 0. SESSION
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if token exists in session, POST or GET
    $token = "";
    $uuid = $_SESSION['user_uuid'] ?? ""; // Get UUID from session if available
    $user_email = $_SESSION['user_email'] ?? ""; // Get email from session if available 

    //$created_by_uuid = $_SESSION['user_uuid'] ?? ""; // Get supervisor_uuid from session if available


    if (isset($_SESSION['access_token'])) {
        $token = $_SESSION['access_token'];
    } elseif (isset($_POST['token'])) {
        $token = $_POST['token'];
        $_SESSION['access_token'] = $token;
    } elseif (isset($_GET['token'])) {
        $token = $_GET['token'];
        $_SESSION['access_token'] = $token;
    }

    // If no token is found, redirect to login page
    if (empty($token)) {
        header('Location: index.php');
        //header('Location: login.php');
        exit();
    }

// La funcion de envio de correo se encuentra en la API de tickets
// requiere 5 campos: $ticket, $from, $to, $subject, $content

// 1. Gather Input (for example, from POST data)

    $ticketTitle = $_POST['title'] ?? '';
    $customer_name_selected = $_POST['customer_name_selected'] ?? '';
    $selected_site_name = $_POST['selected_site_name'];
    $priority = $_POST['priority'] ?? '';
    $worktype = $_POST['worktype'] ?? '';
    $alarmtype = $_POST['alarmtype'] ?? '';
    $status = $_POST['status'] ?? 'nuevo';
    $description = $_POST['description'] ?? '';
    $supervisor_user_name = $_POST['supervisor_user_name'] ?? 'vacio';
    $tecnico_user_name = $_POST['tecnico_user_name'];
    $created_by_user_name = $_POST['created_by_user_name'];

    //$fromAddress = $_POST['from'] ?? '';
    $fromAddress = 'sistema@ots.davidmontoya.utpl.edu.ec';
    //$toAddress = 'usuario@ots.davidmontoya.ec';
    $tecnico_user_email = $_POST['tecnico_user_email'];
    $emailSubject = 'Notificacion de Orden de Trabajo';
    $emailContent = 'Orden de Trabajo';

    // Validate the data (you can add more validation as needed)
    //if (empty($name) || empty($email) || empty($role) || empty($uuid)) {
    //    echo json_encode(['error' => 'All fields are required.']);
    //    exit;
    //}

    // Send the email (this is a placeholder, implement your email sending logic here)
    //$to = $email;
    //$subject = "User Details";
    //$message = "Name: $name\nEmail: $email\nRole: $role\nUUID: $uuid";
    //$headers = "From:

//    --------------------------TEST---------------------------

  
// $ticketTitle = 'Microonda Sectorial 1';
// $customer_name_selected = 'CLARO';
// $selected_site_name = 'Carretas';
// $priority = 'bajo';
// $worktype = 'telecom';
// $alarmtype = 'hardware';
// $status = 'asignado';
// $description = 'Nuevo vulcanizado en sitio for humedad. Laudantium minima aut doloribus similique. ';
// $supervisor_user_name = 'Supervisor 1';
// $tecnico_user_name = 'tecnico 1';
// $created_by_user_name = 'David';

// $fromAddress = 'notificacionesdetickets@gmail.com';
// $tecnico_user_email = 'tecnicouno@gmail.com';
// $emailSubject = 'Notificacion de Orden de Trabajo';
// $emailContent = 'Content: - Orden de Trabajo Asignada';

//    --------------------------TEST---------------------------   

    
// 2. Construct the JSON Payload
    $postData = [
        'ticket' => [
            'title' => $ticketTitle,
            'customer_name_selected' => $customer_name_selected,
            'selected_site_name' => $selected_site_name,
            'priority' => $priority,
            'worktype' => $worktype,
            'alarmtype' => $alarmtype,
            'status' => $status,
            'description' => $description,
            'supervisor_user_name' => $supervisor_user_name,
            'tecnico_user_name' => $tecnico_user_name,
            'created_by_user_name' => $created_by_user_name,

        ],
        'from' => $fromAddress,
        'to' => $tecnico_user_email,
        'subject' => $emailSubject,
        'content' => $emailContent,
    ];

    $jsonData = json_encode($postData);



// 3. Make the HTTP Request using curl

    $apiUrl = 'http://mail-service/api/v1/emails';
    $ch = curl_init($apiUrl);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json')       
    );


    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "Error sending email: $error_msg\n";
    } else {
        // 4. Handle the API Response
        $error_message = "No hay token o existe un error. Error: Failed to fetch tickets (HTTP Code: $httpcode)";
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //echo "HTTP Status Code: $httpCode\n";
        //echo "API Response: $response\n";

        // You might want to decode the JSON response if the API returns data
        //$responseData = json_decode($response, true);
        //var_dump($responseData);
    }

    curl_close($ch);

// con funcion de envio de correo
    // function sendEmailNotification($title, $fromAddress, $toAddress, $emailSubject, $emailContent) {
    //     $postData = [
    //         'ticket' => [
    //             'title' => $title,
    //         ],
    //         'from' => $fromAddress,
    //         'to' => $toAddress,
    //         'subject' => $emailSubject,
    //         'content' => $emailContent,
    //     ];
    
    //     $jsonData = json_encode($postData);
    
    //     $apiUrl = 'http://localhost:8889/api/v1/emails';
    //     $ch = curl_init($apiUrl);
    
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    //     $response = curl_exec($ch);
    
    //     if (curl_errno($ch)) {
    //         $error_msg = curl_error($ch);
    //         echo "Error sending email: $error_msg\n";
    //     } else {
    //         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         echo "HTTP Status Code: $httpCode\n";
    //         echo "API Response: $response\n";
    //     }
    
    //     curl_close($ch);
    // }

//}    

?>

