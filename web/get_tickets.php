<?php

// Start session to preserve token between pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if token exists in session, POST or GET
$token = "";
$uuid = $_SESSION['user_uuid'] ?? ""; // Get UUID from session if available
$user_email = $_SESSION['user_email'] ?? ""; // Get email from session if available  
$user_role = $_SESSION['user_role'] ?? "";

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

echo("<script>console.log('echo Var user_email is " . $user_email . "');</script>");
echo("<script>console.log('echo Var uuid is " . $uuid . "');</script>");
echo("<script>console.log('echo Var token is " . $token . "');</script>");
echo("<script>console.log('echo Var user_role is " . $user_role . "');</script>");
echo("<script>console.log('echo Var --------get_tickets.php------ ');</script>");

// Initialize variables
$tickets = [];
$error_message = "";

// Make API request to get tickets
try {
    // Initialize cURL session
    //$ch = curl_init('http://localhost:8888/api/v1/ots');
    
    $ch= curl_init();
    $api_url = 'http://localhost:8888/api/v1/ots';
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Get current page from URL
    $api_url .= '?page=' . $current_page;  // Append the 'page' parameter

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ));

    //$response = file_get_contents($api_url);

    // Execute the request
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL session
    curl_close($ch);
    
    // Process the response
    if ($httpcode == 200) {
        $tickets = json_decode($result, true);

        $total_pages = $tickets['total_pages'] ?? 1;
        $current_page = $tickets['current_page'] ?? 1;
        $tickets_data = $tickets['tickets'] ?? []; // Get the tickets array

        if (!is_array($tickets)) {
            $error_message = "Error: Invalid response format";
            $tickets = [];
        }
    } else {

        $error_message = "No hay token o no se pudo conectar a la DB. Error: Failed to fetch tickets (HTTP Code: $httpcode)";
    
        // Set the error message in a session variable
        //session_start();
        $_SESSION['error_message'] = $error_message;
        
        // Redirect to a page that will display the message
        header("Location: show_error.php");
        exit;
    }
} catch (Exception $e) {
    $error_message = "Exception: " . $e->getMessage();
}


//echo "<pre>";
//print_r($tickets);
//echo "</pre>";

// Option 2: Using var_dump() for more detailed output
//echo "<pre>";
//var_dump($tickets);
//echo "</pre>";

// Option 3: Converting to JSON (compact, but less readable)
//echo json_encode($tickets);

//echo "<script>console.log('tickets: " . json_encode($tickets) . "');</script>"; 

// Limit to 10 tickets
//$tickets = array_slice($tickets, 0, 10); // Get the first 10 elements
$tickets = array_slice($tickets, -5, 5); // Get the last 5 elements
$tickets = array_reverse($tickets);      // Reverse the order of those 10 elements

?>

<script>
<?php if (isset($tickets)): ?>
    const tickets = <?php echo json_encode($tickets); ?>;
    console.log('get_ticket tickets array', tickets);
<?php endif; ?>
</script>