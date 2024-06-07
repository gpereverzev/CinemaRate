<?php
require 'E:/СМП/cinemaRate/backend/Film.php';
require 'E:/СМП/cinemaRate/backend/User.php';

// Обробка зображення фільму
if (isset($_GET['film_id'])) {
    $film_id = $_GET['film_id'];
    $film = Film::getFilmById($film_id);

    if ($film && isset($film['poster'])) {
        $imageData = $film['poster'];

        // Використання getimagesize для визначення MIME типу зображення
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tmpFilePath, $imageData);
        $imageInfo = getimagesize($tmpFilePath);
        unlink($tmpFilePath);

        if ($imageInfo) {
            $mimeType = $imageInfo['mime'];
            header('Content-Type: ' . $mimeType);
            echo $imageData;
        } else {
            http_response_code(404);
            echo "Зображення не знайдено.";
        }
    } else {
        http_response_code(404);
        echo "Зображення не знайдено.";
    }
    exit;
}

// Обробка аватарки користувача
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Отримання даних користувача за ID
    $user = User::getUserById($user_id);

    if ($user && $user->getAvatar()) {
        $avatar = $user->getAvatar();

        // Використання getimagesize для визначення MIME типу зображення
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tmpFilePath, $avatar);
        $imageInfo = getimagesize($tmpFilePath);
        unlink($tmpFilePath);

        if ($imageInfo) {
            $mimeType = $imageInfo['mime'];
            header('Content-Type: ' . $mimeType);
            echo $avatar;
        } else {
            http_response_code(404);
            echo "Зображення не знайдено.";
        }
    } else {
        // Якщо користувач або аватар не знайдені, відправити стандартне зображення
        header('Content-Type: image/png');
        readfile('default-avatar.png');
    }
    exit;
} else {
    // Якщо ID користувача не вказаний, відправити стандартне зображення
    header('Content-Type: image/png');
    readfile('default-avatar.png');
    exit;
}
?>
