<?php
    session_start();
    $pageTitle = 'User Profile';
?>

<?php
    include('templates/header.php');
?>

<!-- <link rel="stylesheet" href="css/menu.css"> -->
<link rel="stylesheet" href="css/profile.css">

</head>

<body>

<!-- el menu sidenav va despues del body -->
<!-- <div class="sidenav"> -->
<?php 
include('templates/menu.php'); 
?> 

<?php 

    // Check if token exists in session, POST or GET
    $token = "";
    $uuid = $_SESSION['user_uuid'] ?? ""; // Get UUID from session if available
    $user_email = $_SESSION['user_email'] ?? ""; // Get email from session if available  

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
?> 

    <h2>Detalle de Usuario</h2>
    <p>Bienvenido a su perfil!</p>
    <!-- <blink>Espere un momento hasta cargar los datos</blink> -->

    <!-- //BLINK -->
    <SCRIPT type="text/javascript">
        function JavaBlink() {
            var blinks = document.getElementsByTagName('JavaBlink');
            for (var i = blinks.length - 1; i >= 0; i--) {
                var s = blinks[i];
                s.style.visibility = (s.style.visibility === 'visible') ? 'hidden' : 'visible';
            }
            window.setTimeout(JavaBlink, 500);
        }
        if (document.addEventListener) document.addEventListener("DOMContentLoaded", JavaBlink, false);
            else if (window.addEventListener) window.addEventListener("load", JavaBlink, false);
            else if (window.attachEvent) window.attachEvent("onload", JavaBlink);
            else window.onload = JavaBlink;
    </SCRIPT>

    Un momento. <SPAN STYLE="color:#379e2e"><JavaBlink>Espere la carga </JavaBlink></SPAN> de los datos.

    
    <!-- //TABLA USER PROFILE -->
    <div id="user-table-container">

        <!-- //Here we are using function fetchUserDetails() 
        to get the user details from the API and display them in a table. -->
    
    </div>

    <!-- //Function to fetch user details from the API and display them in a table -->
    <script>
        function fetchUserDetails(uuid, accessToken) {
            const apiUrl = `get_profile.php?uuid=${uuid}`; // Send UUID via GET

            fetch(apiUrl, {
                method: 'GET',
                // You might need to include headers for Authorization if your PHP needs an access token
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data); // Log the entire JSON response

                // Create HTML table
                const table = document.createElement('table');
                const thead = document.createElement('thead');
                const tbody = document.createElement('tbody');
                const headerRow = document.createElement('tr');

                // Define table headers
                const headers = ['Name', 'Email', 'Role', 'UUID'];
                headers.forEach(headerText => {
                    const th = document.createElement('th');
                    th.textContent = headerText;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Create table body row with data
                const dataRow = document.createElement('tr');
                const nameCell = document.createElement('td');
                nameCell.textContent = data.name;
                const emailCell = document.createElement('td');
                emailCell.textContent = data.email;
                const roleCell = document.createElement('td');
                roleCell.textContent = data.role;
                const uuidCell = document.createElement('td');
                uuidCell.textContent = data.uuid;

                dataRow.appendChild(nameCell);
                dataRow.appendChild(emailCell);
                dataRow.appendChild(roleCell);
                dataRow.appendChild(uuidCell);
                tbody.appendChild(dataRow);
                table.appendChild(tbody);

                // Append the table to the container in the HTML
                const tableContainer = document.getElementById('user-table-container');
                tableContainer.appendChild(table);

            })
            .catch(error => {
                console.error('Error fetching user details:', error);
                const tableContainer = document.getElementById('user-table-container');
                tableContainer.textContent = 'Error loading user details.';
            });
        }

        // Example usage: Replace with the actual UUID
        var userUUID = "<?php echo $uuid; ?>";         
        var userAccessToken = "<?php echo $token; ?>"; 

        fetchUserDetails(userUUID, userAccessToken);
    </script>

<?php
    include('templates/footer.php');
?>
