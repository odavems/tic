<!doctype html>
<?php

    $pageTitle = 'Login';
    // include('templates/header.php');
?>

<?php 
//session_start();
include('get_login.php'); ?>


<!-- <link rel="stylesheet" href="css/menu.css"> -->
<link rel="stylesheet" href="css/login.css">


</head>

<body>
    
    <h2>Login al sistema. (DB micro_auth)</h2>
    <!-- <div class="container"> -->
        <form method="POST" action="get_login.php">
            <div class="form-group" >
                <label for="email">Email:  dmontoya1@test.com</label>
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
            <?php endif; ?>
        <?php endif; ?>
    </script>


<?php
    include('templates/footer.php');
?>