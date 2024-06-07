<?php
require_once 'User.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

if (!isset($_GET['action'])) {
    echo json_encode(['error' => 'No action specified']);
    exit();
}

$action = $_GET['action'];

switch ($action) {
    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        $login = $data['login'] ?? null;
        $password = $data['password'] ?? null;

        if (!$login || !$password) {
            echo json_encode(['error' => 'Login and password required']);
            break;
        }

        $user = User::authenticate($login, $password);

        if ($user) {
            echo json_encode(['user_id' => $user->getUserId()]);
        } else {
            echo json_encode(['error' => 'Invalid credentials']);
        }
        break;

    case 'register':
        $data = json_decode(file_get_contents('php://input'), true);
        $login = $data['login'] ?? null;
        $password = $data['password'] ?? null;
        $username = $data['username'] ?? null;
        $avatar = $data['avatar'] ?? null;

        if (!$login || !$password || !$username) {
            echo json_encode(['error' => 'All fields are required for registration']);
            break;
        }

        if (User::register($login, $password, $username, $avatar)) {
            echo json_encode(['success' => 'User registered successfully']);
        } else {
            echo json_encode(['error' => 'Registration failed']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
