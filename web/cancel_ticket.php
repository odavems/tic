
<?php

session_start();

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

    //echo("<script>console.log('echo Var Email is " . $email . "');</script>");
    //echo("<script>console.log('echo Var uuid is " . $uuid . "');</script>");
    //echo("<script>console.log('echo Var token is " . $token . "');</script>");


$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "micro_ots";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ticket_id = $_GET['id'] ?? null; // Get the ticket ID from the query string
//$status = $_GET['status'] ?? null;   

if ($ticket_id === null) {
    echo "Error: Ticket ID not provided.";
} else  {
    // First, check the current status of the ticket
    $status_check_sql = "SELECT status FROM tickets WHERE ticket_id = ?";
    $status_check_stmt = $conn->prepare($status_check_sql);
    $status_check_stmt->bind_param("i", $ticket_id);
    $status_check_stmt->execute();
    $status_check_result = $status_check_stmt->get_result();

    if ($status_check_result->num_rows > 0) {
        $row = $status_check_result->fetch_assoc();
        $current_status = strtolower($row['status']);

        if ($current_status == 'cancel') {
            echo "Ticket #" . $ticket_id . " esta ya cancelado.";
        } else {
            // Proceed with the cancellation
            $sql = "UPDATE tickets SET status = 'cancelado' WHERE ticket_id = ?";  // Use a prepared statement!
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $ticket_id);  // "i" indicates an integer
            if ($stmt->execute()) {
                echo "Ticket #" . $ticket_id . " cancelado correctamente.";

                // Add entry to ticket history
                $action = "ticket_cancelado";
                $new_value = "Ticket #$ticket_id cancelado";
                
                $stmt = $conn->prepare("INSERT INTO Ticket_History (ticket_id, user_uuid, action, old_value, new_value) 
                                    VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $ticket_id, $uuid, $action, $current_status, $new_value);
                $stmt->execute();

            } else {
                echo "Error cancelling ticket: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>
