<?php
//session_start();
$error_message = "";
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>



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


 <div class="error-popup" id="errorPopup">
    <?php echo htmlspecialchars(!empty($error_message) ? $error_message : 'ERROR DESCONOCIDO   '); ?>
    ----- Re Direccion en  <span id="countdownNumber">  3  </span> segundos...
</div>

<div id="countdown">Redirecting in <span id="countdownNumber">3</span> seconds...</div>

<script>
    let countdown = 3;
    const countdownElement = document.getElementById('countdownNumber');
    
    // Update countdown every second
    const countdownInterval = setInterval(function() {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            window.location.href = "index.php";
        }
    }, 1000);
</script>

