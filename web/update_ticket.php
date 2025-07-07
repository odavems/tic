<?php
     if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $pageTitle = 'Create Ticket';
    include('templates/header.php');
?>

<link rel="stylesheet" href="css/update_ticket.css">

</head>

<body>

<?php 
include('templates/menu.php');
?> 

<?php 

        // Check if token exists in session, POST or GET
        $token = "";
        $uuid = $_SESSION['user_uuid'] ?? ""; // Get UUID from session if available
        $user_email = $_SESSION['user_email'] ?? ""; // Get email from session if available
        $user_role = $_SESSION['user_role'] ?? "";

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
?> 

<div class="container">
        <h1>Actualizar Orden de Trabajo</h1>

        <?php 

            $servername = "db_server22_ot"; // Use the service name defined in docker-compose.yml
            $username = "root";
            $password = "root";
            $dbname = "micro_ot";
    
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $ticket_id = $_GET['id'] ?? null; // Get ticket ID from URL
            if ($ticket_id === null) {
                echo "Error: Ticket ID not provided.";
                exit; // Stop execution if no ID
            }


            $created_by_uuid = $_SESSION['created_by_uuid'][$ticket_id] ?? 'Unknown'; // Retrieve from session
            
            $site_name = $_SESSION['site_name'][$ticket_id] ?? 'Unknown'; // Retrieve from session

            $escaped_supervisor_name = $_SESSION['escaped_supervisor_name'][$ticket_id] ?? 'Unknown';;  // Retrieve from session
            $escaped_assigned_to_name = $_SESSION['escaped_assigned_to_name'][$ticket_id] ?? 'Unknown';;   // Retrieve from session
    
            //$_SESSION['ticket_id'] =  htmlspecialchars($ticket['ticket_id'] ?? 'N/A');

            // Fetch ticket data
            $sql = "SELECT ticket_id, title, description, status, worktype, alarmtype, priority, 
                    customer_id, site_id, created_by_uuid, assigned_to_uuid, supervisor_uuid
                    FROM tickets WHERE ticket_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo "Error: Ticket not found.";
                exit;
            }

            $ticket = $result->fetch_assoc();
            $current_status = $ticket['status'];

            //$possible_statuses = ['en_progreso','en_espera','resuelto','cerrado']; // Define possible statuses

                if ($user_role === 'tecnico') {
                    $possible_statuses = ['en_progreso','en_espera','resuelto'];
                } else {
                $possible_statuses = ['en_progreso','en_espera','resuelto','cerrado'];
                }
    
    
            // Process form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_status = $_POST['status'] ?? '';
                $comment_content = $_POST['comment'] ?? '';

                // Validate data (basic example)
                if (empty($new_status)) {
                    $error_message = "El Estado del ticket es requerido.";
                } else {
                    // Update ticket status
                    $update_sql = "UPDATE tickets SET status = ? WHERE ticket_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("si", $new_status, $ticket_id);

                    // Add entry to ticket history
                    $action = "ticket_actualizado";
                    //$new_value = "Ticket #$ticket_id cancelado";
                    $old_value = $current_status;
                    $new_value = $new_status;
                

                    $stmt = $conn->prepare("INSERT INTO ticket_history (ticket_id, user_uuid, action, old_value, new_value) 
                                        VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("issss", $ticket_id, $uuid, $action, $old_value, $new_value);
                    $stmt->execute();

                    if ($update_stmt->execute()) {
                        $success_message = "Estado del Ticket actualizado correctamente.";
                        // echo "<div class='alert alert-success'>OT Nro " . $result["ticket_id"] . "actualizada correctamente " . "</div>";
                        echo "<script>alert('Ticket actualizado correctamente!'); window.location.href = 'consola.php';</script>";

                        // Insert comment (if provided)
                        if (!empty($comment_content)) {
                            $insert_comment_sql = "INSERT INTO comments (ticket_id, user_uuid, content) VALUES (?, ?, ?)";
                            $insert_comment_stmt = $conn->prepare($insert_comment_sql);
                            $insert_comment_stmt->bind_param("iss", $ticket_id, $uuid, $comment_content);
                            $insert_comment_stmt->execute();
                            $insert_comment_stmt->close();
                        }
                    } else {
                        $error_message = "Error updating ticket status: " . $update_stmt->error;
        
                    }
                    $update_stmt->close();
                }
            }

        $conn->close();

        ?>

    <div class="header">
        <h2>Orden de Trabajo Nro <?php echo htmlspecialchars($ticket['ticket_id']); ?> </h2>
        
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="post">
        
        <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['ticket_id']); ?>">
       
        <label for="title">Titulo:</label> <?php echo htmlspecialchars($ticket['title']); ?><br><br>
        <label for="descripcion">Descripcion:</label> <?php echo htmlspecialchars($ticket['description']); ?><br><br>
        <label for="sitio">Sitio:</label>   <?php echo htmlspecialchars($site_name); ?><br><br>
        <label for="worktype">Tipo de Trabajo:</label>  <?php echo htmlspecialchars($ticket['worktype']); ?><br><br>
        <label for="alarmtype">Tipo de Alarma:</label>   <?php echo htmlspecialchars($ticket['alarmtype']); ?><br><br>
        <label for="priority">Prioridad:</label>    <?php echo htmlspecialchars($ticket['priority']); ?><br><br>
        <label for="assigned_to">Tecnico Asignado:</label>    <?php echo htmlspecialchars($escaped_assigned_to_name); ?><br><br>
        <label for="supervisor">Supervisor Asignado:</label>   <?php echo htmlspecialchars($escaped_supervisor_name); ?><br><br>

        <!-- Creado por:       <?php //echo htmlspecialchars($ticket_id) . ": " . htmlspecialchars($created_by_uuid); ?><br><br> -->

        <label for="status">Status:</label><br>
        <select id="status" name="status">
            <?php foreach ($possible_statuses as $status): ?>
                <option value="<?php echo htmlspecialchars($status); ?>" <?php if ($status == $ticket['status']) echo 'selected'; ?>><?php echo htmlspecialchars(ucfirst($status)); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="comment">Commentario:</label><br>
        <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>

        <input type="submit" class="submit-btn" value="Actualizar OT">
    </form>

<!-- //cierre container    -->
</div>

<?php
include('templates/footer.php');
?>