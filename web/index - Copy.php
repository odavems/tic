<?php $pageTitle = 'Login';
    session_start();
    // Initialize variables to store messages
    $message = "";
    $token = "";
    $user_email = "";
    $email = "";
    $user_uuid = "";
    $user_role = "";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle ?? 'TITULO'; ?></title>
        <!-- <link rel="stylesheet" href="css/style.css"> -->
        <link rel="stylesheet" href="css/login.css">
    </head>

    <?php
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
        
        putenv('AUTH_SERVICE_BASE_URL=http://localhost:8887/api/v1');
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

    <body>
        <h2>Login al sistema OTs. (DB micro_auth)</h2>
        <!-- <div class="container"> -->
            <!-- <form method="POST" action="get_login.php"> -->
            <form method="POST" action="">
                <div class="form-group" >
                    <label for="email">Email: Superv-> dmontoya1@test.com / Tec-> tecnico1@test.com</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:  12345678</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="wrapper">
                    <!-- <button type="submit">Login</button> -->
                    <button type="submit" class="button button3">Login</button>
                </div>
            </form>
        <!-- </div> -->
        
        <?php if (!empty($message) && strpos($message, 'Error') !== false): ?>
        <div class="message error">
            <p><?php echo $message; ?></p>
            <?php if (!empty($token)): ?>
                <p>Your form user_email is: <?php echo htmlspecialchars($user_email); ?></p>
                <p>Your token is: </p>
                <div class="token"><?php echo htmlspecialchars($token); ?></div>
                <p>Your uuid is: <?php echo htmlspecialchars($uuid); ?></p>
                <p>Your role is: <?php echo htmlspecialchars($role); ?></p>

            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <script>
            // Add JavaScript to log messages to console
            <?php if (!empty($message)): ?>
                console.log("<?php echo $message; ?>");
                <?php if (!empty($token)): ?>
                    console.log("login.php Your user_email is <?php echo $user_email; ?>");
                    console.log("login.php Your token is <?php echo $token; ?>");
                    console.log("login.php Your uuid is <?php echo $uuid; ?>");
                    console.log("login.php Your ROLE is <?php echo $user_role; ?>");
                <?php endif; ?>
            <?php endif; ?>
        </script>

        <footer>
            <p style="text-align: center;">&copy; <?php echo date("Y"); ?> - O. David Montoya S. - UTPL TT TIC </p>
        </footer>

    </body>
</html>