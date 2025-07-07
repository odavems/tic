<?php 
$pageTitle = 'Login';
session_start();

// Initialize variables to store messages
$message = "";
$token = "";
$user_email = "";
$email = "";
$user_uuid = "";
$user_role = "";
$debug_info = [];

// Function to log debug information
function addDebugInfo($key, $value) {
    global $debug_info;
    $debug_info[$key] = $value;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get email and password from the form
        $email = trim($_POST['email'] ?? '');
        $user_email = $email;
        $password = $_POST['password'] ?? '';
        
        addDebugInfo('form_email', $email);
        addDebugInfo('form_submitted', true);
        
        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required");
        }
        
        // Prepare the data for the API request
        $data = array(
            'email' => $email,
            'password' => $password
        );
        $data_string = json_encode($data);
        
        // Enhanced API URL configuration for Docker
        // Try multiple environment variable names and fallback options
        $apiBaseUrl = null;
        $possibleEnvVars = ['AUTH_SERVICE_BASE_URL', 'API_BASE_URL', 'AUTH_API_URL'];
        
        foreach ($possibleEnvVars as $envVar) {
            $apiBaseUrl = getenv($envVar);
            if ($apiBaseUrl) {
                addDebugInfo('api_url_source', $envVar);
                break;
            }
        }
        
        // Fallback URLs for different environments
        if (!$apiBaseUrl) {
            // Check if we're in Docker by looking for common Docker indicators
            if (getenv('DOCKER_ENV') || file_exists('/.dockerenv') || getenv('HOSTNAME')) {
                // In Docker, use service name instead of localhost
                $apiBaseUrl = 'http://auth-serv:80/api/v1';
                addDebugInfo('api_url_source', 'docker_fallback');
            } else {
                // Local development fallback
                $apiBaseUrl = 'http://localhost:80/api/v1';
                addDebugInfo('api_url_source', 'local_fallback');
            }
        }
        
        $fullApiUrl = $apiBaseUrl . '/auth/login';
        addDebugInfo('full_api_url', $fullApiUrl);
        
        // Initialize cURL session with enhanced error handling
        $ch = curl_init($fullApiUrl);
        
        if ($ch === false) {
            throw new Exception("Failed to initialize cURL session");
        }
        
        // Set cURL options with better error handling and timeouts
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false, // For development only
            CURLOPT_VERBOSE => false
        ]);
        
        // Execute the request
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        
        addDebugInfo('http_code', $httpcode);
        addDebugInfo('curl_error', $curl_error);
        addDebugInfo('curl_errno', $curl_errno);
        
        // Close cURL session
        curl_close($ch);
        
        // Check for cURL errors
        if ($result === false || $curl_errno !== 0) {
            throw new Exception("cURL Error ($curl_errno): $curl_error");
        }
        
        addDebugInfo('api_response', $result);
        
        // Process the response
        if ($httpcode == 200) {
            $response = json_decode($result, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON response: " . json_last_error_msg());
            }
            
            if (isset($response['access_token'])) {
                $message = "OK you are logged";
                $token = $response['access_token'];
                $user_uuid = $response['uuid'] ?? '';
                $user_role = $response['user_role'] ?? '';
                
                // Store token in session
                $_SESSION['access_token'] = $token;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_uuid'] = $user_uuid;
                $_SESSION['user_role'] = $user_role;
                
                addDebugInfo('login_success', true);
                addDebugInfo('user_uuid', $user_uuid);
                addDebugInfo('user_role', $user_role);
                
                // Redirect to dashboard
                header("Location: consola.php");
                exit();
            } else {
                throw new Exception("Invalid response from server: missing access_token");
            }
        } else {
            // Handle different HTTP error codes
            $errorMessage = "Authentication failed (HTTP $httpcode)";
            if ($httpcode == 401) {
                $errorMessage = "Invalid email or password";
            } elseif ($httpcode == 500) {
                $errorMessage = "Server error. Please try again later.";
            } elseif ($httpcode == 0) {
                $errorMessage = "Cannot connect to authentication service";
            }
            throw new Exception($errorMessage);
        }
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        addDebugInfo('error_message', $e->getMessage());
        addDebugInfo('error_trace', $e->getTraceAsString());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle ?? 'TITULO'; ?></title>
        <link rel="stylesheet" href="css/login.css">
    </head>

    <body>
        <h2>Login al sistema OTs. (DB micro_auth)</h2>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email: Superv-> dmontoya1@test.com / Tec-> tecnico1@test.com</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password: 12345678</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="wrapper">
                <button type="submit" class="button button3">Login</button>
            </div>
        </form>
        
        <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <p><?php echo htmlspecialchars($message); ?></p>
            <?php if (!empty($token)): ?>
                <p>Your form user_email is: <?php echo htmlspecialchars($user_email); ?></p>
                <p>Your token is: </p>
                <div class="token"><?php echo htmlspecialchars($token); ?></div>
                <p>Your uuid is: <?php echo htmlspecialchars($user_uuid); ?></p>
                <p>Your role is: <?php echo htmlspecialchars($user_role); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Debug information (remove in production) -->
        <?php if (!empty($debug_info) && (getenv('APP_ENV') === 'development' || getenv('DEBUG') === 'true')): ?>
        <div class="debug-info" style="margin-top: 20px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">
            <h3>Debug Information:</h3>
            <pre><?php echo htmlspecialchars(print_r($debug_info, true)); ?></pre>
        </div>
        <?php endif; ?>
        
        <script>
            // Add JavaScript to log messages to console
            <?php if (!empty($message)): ?>
                console.log("<?php echo addslashes($message); ?>");
                <?php if (!empty($token)): ?>
                    console.log("login.php Your user_email is <?php echo addslashes($user_email); ?>");
                    console.log("login.php Your token is <?php echo addslashes($token); ?>");
                    console.log("login.php Your uuid is <?php echo addslashes($user_uuid); ?>");
                    console.log("login.php Your ROLE is <?php echo addslashes($user_role); ?>");
                <?php endif; ?>
            <?php endif; ?>
            
            // Debug information in console
            <?php if (!empty($debug_info)): ?>
                console.log("Debug Info:", <?php echo json_encode($debug_info); ?>);
            <?php endif; ?>
        </script>

        <footer>
            <p style="text-align: center;">&copy; <?php echo date("Y"); ?> - O. David Montoya S. - UTPL TT TIC </p>
        </footer>

    </body>
</html>