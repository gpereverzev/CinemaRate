<?php

class Session {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setUser($user) {
        $_SESSION['user'] = $user;
    }

    public static function getUser() {
        return $_SESSION['user'] ?? null;
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public static function destroy() {
        session_unset();
        session_destroy();
    }
}

?>