<?php
require 'E:/СМП/cinemaRate/backend/User.php';
require 'E:/СМП/cinemaRate/backend/Session.php';

Session::start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $login = $input['login'] ?? '';
    $password = $input['password'] ?? '';

    $user = User::authenticate($login, $password);

    if ($user) {
        Session::setUser($user);
        echo json_encode(['success' => true, 'user_id' => $user->getUserId()]);
    } else {
        echo json_encode(['error' => 'Invalid login or password']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Rate</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Login</h2>
            <form id="loginForm">
                <div class="input-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p id="loginErrorMessage" class="error"></p>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
    <script src="scripts/login.js"></script>
</body>
</html>
