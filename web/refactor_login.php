<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

// --- Controller Logic ---

$message = '';
$user_email = '';
$token = '';
$user_uuid = '';
$user_role = '';

if (request()->isMethod('post')) {
    $request = Request::capture();

    $email = $request->input('email');
    $password = $request->input('password');

    $response = Http::post('http://localhost:8887/api/v1/auth/login', [
        'email' => $email,
        'password' => $password,
    ]);

    if ($response->successful()) {
        $data = $response->json();
        if (isset($data['access_token'])) {
            $message = 'OK you are logged';
            $token = $data['access_token'];
            $user_uuid = $data['uuid'];
            $user_role = $data['user_role'];

            Session::put('access_token', $token);
            Session::put('user_email', $email);
            Session::put('user_uuid', $user_uuid);
            Session::put('user_role', $user_role);

            // In a real Laravel app, you'd redirect to a named route
            // header('Location: /consola');
            // For this example, we'll just show a success message.
        } else {
            $message = 'Error: Invalid response from server';
        }
    } else {
        $message = 'Error: Authentication failed';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <h2>Login al sistema. (DB micro_auth)</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email: Superv-> dmontoya1@test.com / Tec-> tecnico1@test.com</label>
            <input type="email" id="email" name="email" required>
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
        <p><?php echo $message; ?></p>
        <?php if (!empty($token)): ?>
            <p>Your form user_email is: <?php echo htmlspecialchars($user_email); ?></p>
            <p>Your token is: </p>
            <div class="token"><?php echo htmlspecialchars($token); ?></div>
            <p>Your uuid is: <?php echo htmlspecialchars($user_uuid); ?></p>
            <p>Your role is: <?php echo htmlspecialchars($user_role); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <script>
        <?php if (!empty($message)): ?>
            console.log("<?php echo $message; ?>");
            <?php if (!empty($token)): ?>
                console.log("refactor_login.php Your user_email is <?php echo $user_email; ?>");
                console.log("refactor_login.php Your token is <?php echo $token; ?>");
                console.log("refactor_login.php Your uuid is <?php echo $user_uuid; ?>");
                console.log("refactor_login.php Your ROLE is <?php echo $user_role; ?>");
            <?php endif; ?>
        <?php endif; ?>
    </script>

</body>
</html>
