<?php
session_start();
$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <style>
        .error-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            z-index: 1000;
            text-align: center;
            max-width: 80%;
        }
    </style>
</head>
<body>
    <div class="error-popup" id="errorPopup">
        <?php echo htmlspecialchars($error_message); ?>
    </div>

    <script>
        // Wait for 3 seconds and redirect
        setTimeout(function() {
            window.location.href = "index.php";
        }, 3000);
    </script>
</body>
</html>