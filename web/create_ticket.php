<?php
    $pageTitle = 'Create Ticket';
    include('templates/header.php');
?>

<link rel="stylesheet" href="css/create_ticket.css">

</head>

<body>

<?php 
include('templates/menu.php');

?> 

<?php 

    // Start session if not already started
    //--------------------------------------------------------
     if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if token exists in session, POST or GET
    $token = "";
    $uuid = $_SESSION['user_uuid'] ?? ""; // Get UUID from session if available
    $user_email = $_SESSION['user_email'] ?? ""; // Get email from session if available 
    $created_by_uuid = $_SESSION['user_uuid'] ?? ""; // Get supervisor_uuid from session if available


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


    <!-- <script src="get_sites.js"></script> -->
    
    <div class="container">
        <h1>Crear Nueva Orden de Trabajo</h1>


        <!-- PHP -->
        <!------------------------------------------ -->

        <?php


            // Check if form was submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Database connection
                
                $servername = "localhost";
                $username = "root";
                $password = "admin";
                $dbname = "micro_ots";
                
                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                
                // Check connection
                if ($conn->connect_error) {
                    die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
                }
                

                //  --------------  _Get form data
                // <!-- VALORES DE FORMA -->

                $title = $_POST['title'];
                $description = $_POST['description'];
                $status = $_POST['status'] ?? 'nuevo'; // Default to 'nuevo' if not set
                $worktype = $_POST['worktype'] ?? 'telecom'; // Default to 'telecom' if not set
                $alarmtype = $_POST['alarmtype'] ?? 'hardware'; // Default to 'hardware' if not set
                $priority = $_POST['priority'] ?? 'medio'; // Default to 'medio' if not set
                $customer_id = $_POST['customer_id'] ?? 1; 
                $site_id = $_POST['site_id'] ?? 1;
                $created_by_uuid = $_SESSION['user_uuid'];
                $assigned_to_uuid = $_POST['tecnico_user_uuid']; 
                $supervisor_uuid = $_POST['supervisor_user_uuid'];

                //$assigned_to_uuid = $_POST['assigned_to_uuid'] ?? null; // Default to null if not set


                //''''''''''''''''''''''''''''''''''''''''''''''''''
                //FUNCION _CREATE TICKET

                //  _Create ticket function
                function createTicket($conn, $title, $description, $status, $worktype, $alarmtype, 
                                    $priority, $customer_id, $site_id, 
                                    $created_by_uuid, $assigned_to_uuid, $supervisor_uuid) 
                {
                    
 
                    
                    $stmt = $conn->prepare("INSERT INTO Tickets (title, description, status, 
                                        worktype, alarmtype, priority, 
                                        customer_id, site_id, created_by_uuid, assigned_to_uuid, 
                                        supervisor_uuid) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    // _Set initial status based on assignment
                    //$status = ($assigned_to_uuid === null) ? 'nuevo' : 'asignado';
                    
                    $stmt->bind_param("ssssssiisss", $title, $description, $status, $worktype, $alarmtype, 
                    $priority, $customer_id, $site_id, 
                    $created_by_uuid, $assigned_to_uuid, $supervisor_uuid);
                                                    
                    if ($stmt->execute()) {
                        $ticket_id = $conn->insert_id;
                        
                        // Add entry to ticket history
                        $action = "ticket_created";
                        $new_value = "Ticket #$ticket_id created";
                        
                        $stmt = $conn->prepare("INSERT INTO Ticket_History (ticket_id, user_uuid, action, new_value) 
                                            VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isss", $ticket_id, $created_by_uuid, $action, $new_value);
                        $stmt->execute();
                        
                        // If the ticket is assigned, create an assignment record
                        if ($assigned_to_uuid !== null && $supervisor_uuid !== null) {
                            $stmt = $conn->prepare("INSERT INTO Assignments (ticket_id, technician_uuid, supervisor_uuid) 
                                                VALUES (?, ?, ?)");
                            $stmt->bind_param("iss", $ticket_id, $assigned_to_uuid, $supervisor_uuid);
                            $stmt->execute();
                        }
                        
                        return ["success" => true, "ticket_id" => $ticket_id];
                    } else {
                        return ["success" => false, "error" => $stmt->error];
                    }
                }
                
                //Create the ticket
                $result = createTicket(
                    $conn,
                    $title,
                    $description,
                    $status,
                    $worktype,
                    $alarmtype,
                    $priority,
                    $customer_id,
                    $site_id,
                    $created_by_uuid,
                    $assigned_to_uuid,
                    $supervisor_uuid
                );
                

                //''''''''''''''''''''''''''''''''''''''''''''''''''


                // Display result
                if ($result["success"]) {
                    echo "<div class='alert alert-success'>OT creada con Ticket ID: " . $result["ticket_id"] . "</div>";

                    include('send_mail.php');
                    header("Location: consola.php");

                } else {
                    echo "<div class='alert alert-danger'>Error creando ticket: " . $result["error"] . "</div>";
                }
                
                $conn->close();
                
                
                //$insertSuccess = true; // Set based on insert result
                // Send email notification (you can implement this function as needed)
                //if ($insertSuccess) {
                //    include 'send_mail_functions.php';
                //    sendEmailNotification($_POST['title'] ?? '', $_POST['from'] ?? '', $_POST['to'] ?? '', $_POST['subject'] ?? '', $_POST['content'] ?? '');
                //    echo "Data inserted successfully and email sent!";
                //} else {
                //    echo "Error inserting data.";
                //}
            }
        
    //--------------------------------------------------------------------    

            // Function to get users by role
            function getUsersByRole($role) {

                global $token;
        
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
                
                // Close cURL session
                curl_close($ch);
                
                // Parse the JSON response
                $users = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Failed to parse API response: " . json_last_error_msg());
                }
                
                // Filter users by role
                $filteredUsers = [];
                foreach ($users as $user) {
                    if (isset($user['role']) && $user['role'] === $role) {
                        // Use UUID as key instead of user_id
                        //$filteredUsers[$user['id']] = $user['name'];
                         $filteredUsers[$user['id']] = [
                             'name' => $user['name'],
                             'uuid' => $user['uuid'],
                             'email' => $user['email'],
                         ];
                    }
                }
                
                return $filteredUsers;
        
            }
        
            $supervisors = getUsersByRole('supervisor');
            $technicians = getUsersByRole('tecnico');


            // Function to get all customers
            function getCustomers($conn) {
                $customers = [];
                $sql = "SELECT customer_id, customer_name FROM Customers WHERE active = 1";
                $result = $conn->query($sql);
                
                while ($row = $result->fetch_assoc()) {
                    $customers[$row['customer_id']] = $row['customer_name'];
                }
                
                return $customers;
            }

            
            // SITIOS: Function to get sites by customer
            function getSitesByCustomer($conn, $customer_id) {
                $sites = [];
                $sql = "SELECT site_id, site_name FROM Sites WHERE customer_id = ? AND active = 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $customer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $sites[$row['site_id']] = $row['site_name'];
                }
                
                return $sites;
            }
            


            $servername = "localhost";
            $username = "root";
            $password = "admin";
            $dbname = "micro_ots";
            
            //global $servername, $username, $password, $dbname;
            // Connect to database to get dropdown options
            $conn = new mysqli($servername, $username, $password, $dbname);
            if (!$conn->connect_error) {
                        
                //$supervisors = getUsersByRole($conn, 'supervisor');
                //$technicians = getUsersByRole($conn, 'tecnico');
                $customers = getCustomers($conn);
                $conn->close();
            }

        ?>


        <!-- FORMA -->
        <!-- ------------------------------ -->

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <!-- ---------------TITULO------------------ -->
            <div class="form-group">
                <label for="title">Titulo</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Descripcion:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            
             <!-- ---------------PRIORIDAD------------------ -->
            <div class="form-group">
                <label for="priority">Prioridad:</label>
                <select type="text" id="priority" name="priority" required>
                    <option value="bajo">bajo</option>
                    <option value="medio" selected>medio</option>
                    <option value="alto">alto</option>
                    <option value="critico">crtico</option>
                </select>
            </div>
  
             <!-- ---------------TIPO DE TRABAJO------------------ -->
            <div class="form-group">
                <label for="worktype">Tipo de Trabajo:</label>
                <select type="text" id="worktype" name="worktype" required>
                    <option value="electrico">electrico</option>
                    <option value="telecom" selected>telecom</option>
                    <option value="planta_externa">planta_externa</option>
                    <option value="civil">civil</option>
                </select>
            </div>

            <!-- ---------------TIPO DE ALARMA------------------ -->
            <div class="form-group">
                <label for="alarmtype">Tipo de Alarma:</label>
                <select type="text" id="alarmtype" name="alarmtype" required>
                    <option value="hardware" selected>hardware</option>
                    <option value="software">software</option>
                    <option value="red">red</option>
                    <option value="seguridad">seguridad</option>
                </select>
            </div>


            <!-- ---------------CLIENTE------------------ -->
            <div class="form-group">
                <label for="customer_id">Cliente:</label>
                <select id="customer_id" name="customer_id" required>
                    <option value="">Seleccionar Cliente</option>
                    <?php if(!empty($customers)): ?>
                        <?php foreach($customers as $id => $customerName): ?>
                            <?php 
                            // Check if $customerName is an array or a direct string
                            $displayCustomerName = is_array($customerName) ? ($customerName['name'] ?? '') : $customerName;
                            ?>

                            <option value="<?php echo $id; ?>"
                                data-customer-name="<?php echo htmlspecialchars($displayCustomerName); ?>">
                                <?php echo htmlspecialchars($displayCustomerName); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    //solo guarda el ultimo nombre de cliente fuera del loop
                    <?php $customer_last_name_selected = $displayCustomerName; ?>
               
                </select>
            <input type="hidden" id="customer_name_selected" name="customer_name_selected" value="">
            </div>

            <!-- SCRIPT PARA GUARDAR EL NOMBRE DEL CLIENTE -->
            <script>
                // JavaScript to update the hidden field when a customer is selected
                document.getElementById('customer_id').addEventListener('change', function() {
                    var selectedOption = this.options[this.selectedIndex];
                    var customerName = selectedOption.getAttribute('data-customer-name');
                    document.getElementById('customer_name_selected').value = customerName;
                });

                // Initialize the hidden field with the default selected option (if any)
                window.addEventListener('DOMContentLoaded', function() {
                    var customerSelect = document.getElementById('customer_id');
                    if (customerSelect.selectedIndex > 0) {
                        var selectedOption = customerSelect.options[customerSelect.selectedIndex];
                        var customerName = selectedOption.getAttribute('data-customer-name');
                        document.getElementById('customer_name_selected').value = customerName;
                    }
                });
            </script>



            <!-- ---------------SITIO------------------ -->
            <div class="form-group">
                <label for="site_id">Sitio:</label>
                <select id="site_id" name="site_id" required>
                    <option value="">Seleccionar Sitio</option>
                    <!-- Sites will be populated via JavaScript when customer is selected -->
                    <!-- Create a file named "get_sites.php" that returns sites for a given customer 
                    in JSON format, or modify the JavaScript to handle site selection differently-->
                    <!-- From get_sites.php you get a JSON $sites array -->
                </select>
                <input type="hidden" id="selected_site_name" name="selected_site_name" value="">
            </div>

            
            <!-- ---------------SUPERVISOR------------------ -->
            <div class="form-group">
                <label for="supervisor_id">Supervisor Asignado:</label>
                <select id="supervisor_id" name="supervisor_id" required>
                    <option value="">Selecccionar Supervisor</option>
                    <?php if(isset($supervisors)): ?>
                        <?php foreach($supervisors as $id => $supervisorData): ?>
                                                        
                            <option value="<?php echo $id; ?>" 
                                supervisor-user-uuid="<?php echo htmlspecialchars($supervisorData['uuid'] ?? ''); ?>" 
                                supervisor-user-name="<?php echo htmlspecialchars($supervisorData['name'] ?? ''); ?>"> 
                                <?php echo htmlspecialchars($supervisorData['name']); ?>
                            </option>

                        <?php endforeach; ?>
                    <?php endif; ?>

                </select>
                <input type="hidden" id="supervisor_user_uuid" name="supervisor_user_uuid">
                <input type="hidden" id="supervisor_user_name" name="supervisor_user_name">

            </div>

                <!-- SCRIPT PARA ELEGIR NOMBRE DE SUPERVISOR -->

                <script>
                    const supervisorSelect = document.getElementById('supervisor_id');
                    const supervisorUserUuid = document.getElementById('supervisor_user_uuid');
                    const supervisorUserName = document.getElementById('supervisor_user_name');

                    supervisorSelect.addEventListener('change', function() {
                        const selectedValue = supervisorSelect.value;
                        const selectedOption = supervisorSelect.options[supervisorSelect.selectedIndex];
                        const supervisoruserUuid = selectedOption.getAttribute('supervisor-user-uuid');
                        const supervisoruserName = selectedOption.getAttribute('supervisor-user-name');
 
                        console.log('Selected Supervisor ID:', selectedValue);
                        console.log('Selected Supervisor UUID:', supervisoruserUuid);
                        console.log('Selected Supervisor Name:', supervisoruserName);

                        supervisorUserUuid.value = supervisoruserUuid; // Store user_ in the hidden field
                        supervisorUserName.value = supervisoruserName;

                        // If you need to see the whole supervisor object:
                        <?php if (isset($supervisors)): ?>
                            const supervisors = <?php echo json_encode($supervisors); ?>;
                            console.log('supervisors array', supervisors);
                        <?php endif; ?>
                    });

                </script>

            
            <!-- ---------------TECNICO------------------ -->
            <div class="form-group">
                <label for="tecnico_id">Tecnico Asignado:</label>
                <select id="tecnico_id" name="tecnico_id">
                    <option value="">Seleccionar Tecnico</option>
                    <?php if(isset($technicians)): ?>
                        <?php foreach($technicians as $id => $technicianData): ?>

                            <option value="<?php echo $id; ?>" 
                                tecnico-user-uuid="<?php echo htmlspecialchars($technicianData['uuid'] ?? ''); ?>" 
                                tecnico-user-name="<?php echo htmlspecialchars($technicianData['name'] ?? ''); ?>"
                                tecnico-user-email="<?php echo htmlspecialchars($technicianData['email'] ?? ''); ?>"> 
                                > <?php echo htmlspecialchars($technicianData['name']); ?>
                            </option>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <input type="hidden" id="tecnico_user_uuid" name="tecnico_user_uuid">
                <input type="hidden" id="tecnico_user_name" name="tecnico_user_name">
                <input type="hidden" id="tecnico_user_email" name="tecnico_user_email">
            </div>

                <!-- SCRIPT PARA ELEGIR NOMBRE DE TECNICO -->
                <script>
                    const tecnicoSelect = document.getElementById('tecnico_id');
                    const tecnicoUserUuid = document.getElementById('tecnico_user_uuid');
                    const tecnicoUserName = document.getElementById('tecnico_user_name');
                    const tecnicoUserEmail = document.getElementById('tecnico_user_email');

                    tecnicoSelect.addEventListener('change', function() {
                        const selectedValue = tecnicoSelect.value;
                        const selectedOption = tecnicoSelect.options[tecnicoSelect.selectedIndex];
                        const tecnicouserUuid = selectedOption.getAttribute('tecnico-user-uuid');
                        const tecnicouserName = selectedOption.getAttribute('tecnico-user-name');
                        const tecnicouserEmail = selectedOption.getAttribute('tecnico-user-email');
 
                        console.log('Selected Tecnico ID:', selectedValue);
                        console.log('Selected Tecnico UUID:', tecnicouserUuid);
                        console.log('Selected Tecnico Name:', tecnicouserName);
                        console.log('Selected Tecnico Email:', tecnicouserEmail);

                        tecnicoUserUuid.value = tecnicouserUuid; // Store user_ in the hidden field
                        tecnicoUserName.value = tecnicouserName;
                        tecnicoUserEmail.value = tecnicouserEmail;

                        // If you need to see the whole supervisor object:
                        <?php if (isset($technicians)): ?>
                            const technicians = <?php echo json_encode($technicians); ?>;
                            console.log('technicians array', technicians);
                        <?php endif; ?>
                    });

                </script>

 
            
            <!-- ---------------CREADO POR------------------ -->
            <label for="creado_por">Creado Por:</label>
                <?php
                    $created_by_uuid = $_SESSION['user_uuid'] ?? "";
                    $user_name_found = "UUID not found"; // Default value

                    foreach ($supervisors as $id => $supervisor) {
                        if ($supervisor['uuid'] === $created_by_uuid) {
                            $user_name_found = $supervisor['name'];
                            break; // Exit the loop once found
                        }
                    }
                    // For debugging
                    //echo "Supervisor Name: " . $user_name_found;
                    echo "<script>console.log('supervisors: " . json_encode($supervisors) . "');</script>";
                    echo "<script>console.log('Supervisor user_name_found: " . $user_name_found . "');</script>";
                ?>
                <input type="text" class="form-control" id="user_name_found" name="user_name_found" 
                    value="<?php echo htmlspecialchars($user_name_found); ?>" readonly>
                <input type="hidden" name="created_by_uuid_value" value="<?php echo htmlspecialchars($created_by_uuid); ?>">
                <input type="hidden" id="created_by_user_name" name="created_by_user_name" value="<?php echo htmlspecialchars($user_name_found); ?>">

                <!-- If you need JavaScript to access this value later, you can use: -->
                <script>
                    // Store the PHP value as a JavaScript variable if needed for later use
                    var userNameFound = "<?php echo htmlspecialchars(addslashes($user_name_found)); ?>";
                    console.log("userNameFound:", userNameFound);
                    
                    // No need to set the value again as it's already set by PHP
                    // If you need to update it later for some reason:
                    document.getElementById('created_by_user_name').value = userNameFound;
                </script>



          <br>
          <button type="submit" class="submit-btn">Crear Ticket</button>

        </form>


    <!-- //cierre container    -->
    </div>


    <?php

        //echo "<pre>";
        //print_r($supervisors);
        //echo "</pre>";

        // _Option 2: Using var_dump() for more detailed output
        //echo "<pre>";
        //var_dump($supervisors);
        //echo "</pre>";

        // _Option 3: Converting to JSON (compact, but less readable)
        //echo json_encode($supervisors);

        // PHP debugging:
        // echo "<script>console.log('title: " . json_encode($title) . "');</script>";
        // echo "<script>console.log('description: " . json_encode($description) . "');</script>";
        // echo "<script>console.log('status: " . json_encode($status) . "');</script>";
        // echo "<script>console.log('worktype: " . json_encode($worktype) . "');</script>";
        // echo "<script>console.log('alarmtype: " . json_encode($alarmtype) . "');</script>";
        // echo "<script>console.log('priority: " . json_encode($priority) . "');</script>";
        // echo "<script>console.log('customer_id: " . json_encode($customer_id) . "');</script>";
        // echo "<script>console.log('site_id: " . json_encode($site_id) . "');</script>";
        // echo "<script>console.log('created_by_uuid: " . json_encode($created_by_uuid) . "');</script>";
        // echo "<script>console.log('assigned_to_uuid: " . json_encode($assigned_to_uuid) . "');</script>";
        // echo "<script>console.log('supervisor_uuid: " . json_encode($supervisor_uuid) . "');</script>";

        // echo "<script>console.log('name_found: " . json_encode($name_found) . "');</script>";
        // echo "<script>console.log('supervisors: " . json_encode($supervisors) . "');</script>";        

    ?>


        <!-- //SITES: based on selected customer -->
        <script>
            // JavaScript to populate sites based on selected customer
            document.getElementById('customer_id').addEventListener('change', function() {
                const customerId = this.value;
                const siteDropdown = document.getElementById('site_id');
                
                // Clear current options
                siteDropdown.innerHTML = '<option value="">Select Site</option>';
                
                if (customerId) {
                    // In a real application, this would be an AJAX call to fetch sites
                    // For demonstration, we're showing a placeholder
                    fetch('get_sites.php?customer_id=' + customerId)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(site => {
                                const option = document.createElement('option');
                                option.value = site.id;
                                option.textContent = site.name;
                                siteDropdown.appendChild(option);
                            });

                            siteDropdown.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                             document.getElementById('selected_site_name').value = selectedOption.textContent;
                            });

                        })
                        .catch(error => {
                            console.error('Error fetching sites:', error);
                        });
                }
            });
            
            // Copy supervisor to supervisor_id when selected
            //document.getElementById('created_by_uuid').addEventListener('change', function() {
            //    document.getElementById('supervisor_id_uuid').value = this.value;
            //});

            //console.log('created_by_uuid: ' + <?php //echo json_encode($created_by_uuid); ?>);
            //console.log('Creado por name_found: ' + <?php //echo json_encode($name_found); ?>);
        </script>
    
        
<?php
    include('templates/footer.php');
?>