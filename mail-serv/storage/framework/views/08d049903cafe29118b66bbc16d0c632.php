<?php
session_start(); // Start the session

function loginAndGetToken($usermail, $password, $authApiUrl) {
    // The endpoint of your authentication API
    $loginEndpoint = $authApiUrl . 'v1/auth/login'; // Adjust the endpoint if necessary

    // Data to send in the POST request (your login credentials)
    $postData = json_encode([
        'usermail' => $usermail,
        'password' => $password,
    ]);

    // Initialize cURL session
    $ch = curl_init($loginEndpoint);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_POST, true);        // Set the request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Set the POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Set the content type to JSON
    ]);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "Error during API request: " . $error;
    }

    // Get the HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($ch);

    // Process the response
    if ($httpCode === 200) {
        // Successful login, decode the JSON response
        $responseData = json_decode($response, true);

        // Check if the 'token' key exists in the response
        if (isset($responseData['token'])) {
            return $responseData['token']; // Return the token
        } else {
            return "Error: Token not found in the API response.";
        }
    } else {
        // Login failed, handle the error (you might want to log the $response for more details)
        return "Login failed. HTTP status code: " . $httpCode . ". Response: " . $response;
    }
}

//form 'login'
if (isset($_POST['login'])) {
    // Get the form data
    $usermail = $_POST['usermail'];
    $password = $_POST['password'];

    // API Authentication
    $authApiUrl = 'http://localhost:8887/api/'; // Replace with your actual auth API URL

    $token = loginAndGetToken($usermail, $password, $authApiUrl);

    if (is_string($token) && strpos($token, 'Error') === 0) {
        echo $token . "\n"; // Output the error message
    } elseif ($token) {
        // Login successful!
        echo "Login successful! Token: " . $token . "\n";
        // Store the token in a session
        $_SESSION['token'] = $token;
        $_SESSION['usermail'] = $usermail; // Corrected this line

        // Redirect to the user's dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Login failed for an unknown reason.\n";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ordenes v01</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="background-color:white;">
    <div class="container">
        <h3>Bienvenido al Sistema</h3>
        <p><a href="index.blade.php">LOGIN</a></p>
        <p><a href="register.php">REGISTER</a></p>

        <form action="index.blade.php" method="post">
            <label for="usermail">Usermail:</label>
            <input id="usermail" name="usermail" required="" type="text" />
            <label for="password">Password:</label>
            <input id="password" name="password" required="" type="password" />
            <input name="login" type="submit" value="Login" />
        </form>
    </div>
</body>

</html><?php /**PATH D:\__AE_IT_CODE\__IMPLEMENTACION_TT\laravel_v01\ot-serv\resources\views/index.blade.php ENDPATH**/ ?>