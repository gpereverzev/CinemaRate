<?php

class User
{
    private $user_id;
    private $login;
    private $password;
    private $username;
    private $avatar;

    public function __construct($user_id, $login, $password, $username, $avatar)
    {
        $this->user_id = $user_id;
        $this->login = $login;
        $this->password = $password;
        $this->username = $username;
        $this->avatar = $avatar;
    }

    // Getters
    public function getUserId()
    {
        return $this->user_id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    // Setters
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public static function authenticate($login, $password)
    {
        // Connect to your SQLite database
        $db = new PDO('sqlite:E:/СМП/cinemaRate/src/cinemaRate.db');

        // Prepare SQL statement to fetch user data based on login
        $statement = $db->prepare('SELECT * FROM Users WHERE login = :login');
        $statement->execute(array(':login' => $login));
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            return new User($user['user_id'], $user['login'], $user['password'], $user['username'], $user['avatar']);
        } else {
            return false; // Authentication failed
        }
    }

    public static function register($login, $password, $username, $avatarBase64)
    {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Convert base64 data to binary
        $avatarBinary = base64_decode($avatarBase64);

        // Connect to your SQLite database
        $db = new PDO('sqlite:E:/СМП/cinemaRate/src/cinemaRate.db');

        // Prepare SQL statement to insert new user data
        $statement = $db->prepare('INSERT INTO Users (login, password, username, avatar) VALUES (:login, :password, :username, :avatar)');
        $result = $statement->execute(array(':login' => $login, ':password' => $hashed_password, ':username' => $username, ':avatar' => $avatarBinary));

        if ($result) {
            // Registration successful
            return true;
        } else {
            // Registration failed
            return false;
        }
    }

    public static function getUserById($user_id)
    {
        // Підключення до бази даних
        $pdo = new PDO('sqlite:E:/СМП/cinemaRate/src/cinemaRate.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Запит для отримання даних користувача за ID
        $stmt = $pdo->prepare('SELECT user_id, login, password, username, avatar FROM Users WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return new User($user['user_id'], $user['login'], $user['password'], $user['username'], $user['avatar']);
        }

        return null;
    }
}
