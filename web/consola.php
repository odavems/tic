<?php 
$pageTitle = 'Consola OTs';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    
    include('get_tickets.php');
    include('get_users.php');
    
?>

<?php 
include('templates/header.php');   
?>

<link rel="stylesheet" href="css/consola.css">

</head>

<body>

<?php 
include('templates/menu.php');
?> 
    
    <!--
    <div class="header">
        <a href="create_ticket.php" class="create_ticket">Crear</a>
        <h2>Consola de Ordenes de Trabajo</h2>
        <a href="index.php" class="logout">Logout</a>
    </div>
    -->
    
    <div class="header">
    <?php if ($user_role !== 'tecnico'): ?>
        <a href="create_ticket.php" class="create_ticket">Crear</a>
    <?php endif; ?>
    <h2>Consola de Ordenes de Trabajo</h2>
    <a href="index.php" class="logout">Logout</a>
</div>
      
    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <?php if (empty($tickets)): ?>
        <p>No tickets found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sitio</th>
                    <th>Titulo</th>
                    <th>Tipo</th>
                    <th>Alarma</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Creado al</th>
                    <th>Supervisor</th>
                    <th>Tecnico</th>
                    <th>Actualizar</th>
                    <th>Cancelar</th>

                </tr>
            </thead>
            <tbody>

                <?php 
                // Establish a database connection (You'll need to fill in your credentials)
                $servername = "db_server22_ot"; // Use the service name defined in docker-compose.yml
                $username = "root";
                $password = "root";
                $dbname = "micro_ot";

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

        
                // Function to fetch user name based on UUID                
                function fetchUserName($uuid) {
                    if (isset($_SESSION['users_names_and_uuids'])) {
                        foreach ($_SESSION['users_names_and_uuids'] as $user) {
                            if ($user['uuid'] === $uuid) {
                                return $user['name'] ?? 'N/A';
                            }
                        }
                        return 'N/A'; // Return 'N/A' if the uuid is not found
                    } else {
                        return 'N/A'; // Return 'N/A' if the session variable is not set
                    }
                }

                //foreach ($tickets as $ticket):
                $ticket_count = 0; 
                foreach ($tickets['tickets'] as $ticket):    
                    // Fetch site name from the 'sites' table
                    $site_id = htmlspecialchars($ticket['site_id'] ?? 'N/A');
                    $site_name = "N/A"; // Default value

                    if ($site_id !== 'N/A') {
                        $sql = "SELECT site_name FROM sites WHERE site_id = $site_id"; // Assuming 'id' is the primary key
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $site_name = htmlspecialchars($row["site_name"]);
                        }
                    }

                    // Fetch supervisor and technician names
                    $supervisor_name = fetchUserName($ticket['supervisor_uuid'] ?? '');
                    //echo "Supervisor Name: " . $supervisor_name; // Output
                    $assigned_to_name = fetchUserName($ticket['assigned_to_uuid'] ?? '');
                    
                    $escaped_supervisor_name = htmlspecialchars($supervisor_name);
                    $escaped_assigned_to_name = htmlspecialchars($assigned_to_name);
    
                    // Determine the CSS class based on the status
                    $status_class = '';
                    // if (strtolower($ticket['status']) == 'asignado') {
                    //     $status_class = 'status-asignado';
                    // }else{
                    //     if (strtolower($ticket['status']) == 'resuelto') {
                    //         $status_class = 'status-resuelto';
                    //     }else{
                    //         if (strtolower($ticket['status']) == 'cerrado') {
                    //             $status_class = 'status-cerrado';
                    //         }else{
                    //             $status_class = 'status-otroestado'; // Default
                    //         }
                    //    }
                    // }

                    if (isset($ticket['status']) && strtolower($ticket['status']) == 'en_espera') {  // Updated line with isset()
                        $status_class = 'status-enespera';
                    } else {
                        if (isset($ticket['status']) && strtolower($ticket['status']) == 'resuelto') {  // Updated line with isset()
                            $status_class = 'status-resuelto';
                        } else {
                            if (isset($ticket['status']) && strtolower($ticket['status']) == 'cerrado') {  // Updated line with isset()
                                $status_class = 'status-cerrado';
                            } else {
                                if (isset($ticket['status']) && strtolower($ticket['status']) == 'cancelado') {  // Updated line with isset()
                                    $status_class = 'status-cancelado';
                                } else {    
                                $status_class = 'status-otroestado';
                                // Default
                                }
                            }
                        }
                    }

                    $priority_class = '';
                    // if (strtolower($ticket['priority']) == 'medio') {
                    //     $priority_class = 'priority-medio';
                    // }else{
                    //     if (strtolower($ticket['priority']) == 'alto') {
                    //         $priority_class = 'priority-alto';
                    //     }else{
                    //         if (strtolower($ticket['priority']) == 'critico') {
                    //             $priority_class = 'priority-critico';
                    //         }else{
                    //             $priority_class = 'priority-otroestado';
                    //         }
                    //     }
                    // }

                    if (isset($ticket['priority']) && strtolower($ticket['priority']) == 'medio') {  // Updated line with isset()
                        $priority_class = 'priority-medio';
                    } else {
                        if (isset($ticket['priority']) && strtolower($ticket['priority']) == 'alto') {  // Updated line with isset()
                            $priority_class = 'priority-alto';
                        } else {
                            if (isset($ticket['priority']) && strtolower($ticket['priority']) == 'critico') {  // Updated line with isset()
                                $priority_class = 'priority-critico';
                            } else {
                                $priority_class = 'priority-otroestado';
                            }
                        }
                    }
 
                    // VARIABLES PARA SESSION
                    $_SESSION['site_name'][$ticket['ticket_id']] = $site_name;  // Store in session
                    $_SESSION['escaped_supervisor_name'][$ticket['ticket_id']] = $escaped_supervisor_name;  // Store in session
                    $_SESSION['escaped_assigned_to_name'][$ticket['ticket_id']] = $escaped_assigned_to_name;  // Store in session
                    //$_SESSION['ticket_title'][$ticket['ticket_id']] = htmlspecialchars($ticket['title'] ?? 'N/A');  // Store in session
                    //$_SESSION['ticket_description'][$ticket['ticket_id']] = htmlspecialchars($ticket['description'] ?? 'N/A');  // Store in session 
                    //$_SESSION['ticket_status'][$ticket['ticket_id']] = htmlspecialchars($ticket['status'] ?? 'N/A');  // Store in session
                    //$_SESSION['ticket_worktype'][$ticket['ticket_id']] = htmlspecialchars($ticket['worktype'] ?? 'N/A');  // Store in session
                    //$_SESSION['ticket_alarmtype'][$ticket['ticket_id']] = htmlspecialchars($ticket['alarmtype'] ?? 'N/A');  // Store in session
                    //$_SESSION['ticket_priority'][$ticket['ticket_id']] = htmlspecialchars($ticket['priority'] ?? 'N/A');  // Store in session
                    //$_SESSION['ticket_created_by_uuid'][$ticket['ticket_id']] = $created_by_uuid;  // Store in session     
                    //$_SESSION['ticket_id'] =  htmlspecialchars($ticket['ticket_id'] ?? 'N/A');


                    
                ?>
                    <!-- TABLA DE CONSOLA CON TICKETS -->
                    <tr>
                        <td><?php echo htmlspecialchars($ticket['ticket_id'] ?? 'N/A'); ?></td>
                        <td><?php echo $site_name; ?></td>
                        <td><?php echo htmlspecialchars($ticket['title'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($ticket['worktype'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($ticket['alarmtype'] ?? 'N/A'); ?></td>
                        <td class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($ticket['status'] ?? 'N/A'); ?></td>
                        <td class="<?php echo $priority_class; ?>"><?php echo htmlspecialchars($ticket['priority'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($ticket['created_at'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($supervisor_name); ?></td>
                        <td><?php echo htmlspecialchars($assigned_to_name); ?></td>
                        <td><button onclick="updateTicket(<?php echo htmlspecialchars($ticket['ticket_id'] ?? 0); ?>)"  type="button" class="button" style="background-color: blue; color: white;">Actualizar</button></td>
                        
                        
                         <!--<td><button onclick="cancelTicket(<? // php echo htmlspecialchars($ticket['ticket_id'] ?? 0); ?>)" type="button" class="button" style="background-color: gray; color: white;">Cancelar</button></td> -->
                        
                        <td>
                            <?php if ($user_role === 'tecnico'): ?>
                                <button type="button" class="button" style="background-color: #cccccc; color: #666; cursor: not-allowed;" disabled>
                                Cancelar
                                </button>
                            <?php else: ?>
                                <button onclick="cancelTicket(<?php echo htmlspecialchars($ticket['ticket_id'] ?? 0); ?>)" type="button" class="button" style="background-color: gray; color: white;">
                                Cancelar
                                </button>
                            <?php endif; ?>
                        </td>
                        
                                         
                    </tr>
                    
                <?php 
                $ticket_count++; 
                endforeach; 

                $conn->close(); // Close the database connection

                // echo "<h1>ARRAY DESDE CONSOLA</h1>";
                // echo "<pre>";
                // print_r($users);
                // echo "</pre>";

                ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php if ($current_page > 1): ?>
                    <a href="consola.php?page=<?php echo $current_page - 1; ?>">Pagina Previa</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="consola.php?page=<?php echo $i; ?>" <?php if ($i == $current_page) echo 'class="current"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="consola.php?page=<?php echo $current_page + 1; ?>">Siguiente pagina</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>


    </div>


    <?php endif; ?>
    
    <script>

        function cancelTicket(ticketId) {
            if (confirm("Esta seguro que desea cancelar el ticket #" + ticketId + "?")) {
                // Send an AJAX request to your cancel_ticket.php or API endpoint
                fetch('cancel_ticket.php?id=' + ticketId, {  // Or your API endpoint
                    method: 'GET' // or 'POST' depending on your setup
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Show a message to the user (success or error)
                    location.reload(); // Refresh the page to show the updated status
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while trying to cancel the ticket.');
                });
            }
        }

        function updateTicket(ticketId) {
        // Redirect to the update_ticket.php page, passing the ticketId as a GET parameter
        window.location.href = 'update_ticket.php?id=' + ticketId;
        }


        console.log("---->>> consola.php <<<----");
        console.log("---->>> Dashboard loaded <<<----");
        console.log("Number of tickets displayed: = <?php echo $ticket_count; ?>");
        console.log("Your token is = <?php echo $token; ?>");
        console.log("Your uuid is = <?php echo $uuid; ?>");
        console.log("Your user_email is = <?php echo $user_email; ?>");
        console.log("var escaped_supervisor_name = <?php echo $escaped_supervisor_name; ?>");
        console.log("var supervisor_name = <?php echo $supervisor_name; ?>");
        console.log("var user_role = <?php echo $user_role; ?>");
                
        console.log("---->>> consola.php <<<----");


        
    </script>


<?php
    include('templates/footer.php');
?>