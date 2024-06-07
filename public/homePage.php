<?php
require 'E:/СМП/cinemaRate/backend/Film.php';
require 'E:/СМП/cinemaRate/backend/Session.php';
require 'E:/СМП/cinemaRate/backend/User.php';

Session::start();

if (!Session::isAuthenticated()) {
    echo "Користувач не авторизований.";
    exit;
}

$user = Session::getUser();

if (!$user) {
    echo "Користувач не знайдений.";
    exit;
}

$films = Film::getAllFilms();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Афіша фільмів</title>
    <link rel="stylesheet" href="/styles/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #262A46;
            margin: 0;
            padding: 0;
        }

        .header {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .logo img {
            height: 50px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
        }

        .user-info img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-info .nickname {
            font-size: 16px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        .notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: green;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 9999;
            display: none;
        }

        .notification.error {
            background-color: red;
        }

        .films {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .film-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px;
            width: 220px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            text-decoration: none; /* Прибирає підкреслення */
            color: inherit; /* Встановлює колір тексту такий же, як у батьківського елемента */
        }

        .film-card:hover {
            text-decoration: none; /* Прибирає підкреслення при наведенні */
            color: inherit; /* Встановлює колір тексту такий же, як у батьківського елемента */
        }

        .film-card img {
            width: 100%;
            height: auto;
        }

        .film-info {
            padding: 10px;
            text-align: center;
        }

        .film-info h3 {
            margin: 10px 0;
            font-size: 18px;
        }

        .film-info p {
            margin: 5px 0;
            color: #666;
        }

        .film-info form {
            margin-top: 10px;
        }

        .film-info button {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .film-info button:hover {
            background-color: #555;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo">
            <img src="/src/Logo.svg" alt="Логотип">
        </div>
        <h1>Афіша фільмів</h1>
        <div class="user-info" onclick="toggleDropdown()">
            <?php if ($user) : ?>
                <img src="../backend/image.php?user_id=<?= $user->getUserId() ?>" alt="<?= $user->getUsername() ?>">
                <div class="nickname"><?= htmlspecialchars($user->getUsername()) ?></div>
            <?php endif; ?>
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="/public/my-toplist.php?user_id=<?= $user->getUserId() ?>"> >Мій топлист</a>
            </div>
        </div>
    </header>

    <?php if (isset($_SESSION['message']) || isset($_SESSION['error'])) : ?>
        <div class="notification <?= isset($_SESSION['error']) ? 'error' : '' ?>" id="notification">
            <?= $_SESSION['message'] ?? $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['message']);
        unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="films">
        <?php if (isset($films) && !empty($films)) : ?>
            <?php foreach ($films as $film) : ?>
                <?php
                $title = htmlspecialchars($film['title'] ?? 'Невідомий фільм');
                $genre = htmlspecialchars($film['genre'] ?? 'Жанр не вказано');
                $director = htmlspecialchars($film['director'] ?? 'Режисер не вказаний');
                $film_id = $film['film_id'];
                ?>
                <a href="filmInfo.php?film_id=<?= $film_id ?>" class="film-card">
                    <img src="../backend/image.php?film_id=<?= $film_id ?>" alt="<?= $title ?>">
                    <div class="film-info">
                        <h3><?= $title ?></h3>
                        <p>Жанр: <?= $genre ?></p>
                        <p>Режисер: <?= $director ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Немає фільмів для відображення.</p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 2000);
            }

            const user_id = localStorage.getItem('user_id');
            if (user_id) {
                const userIdElement = document.querySelector('.user-id');
                if (userIdElement) {
                    userIdElement.textContent = `User ID: ${user_id}`;
                }
            }
        });

        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdown-menu');
            if (dropdownMenu.style.display === 'block') {
                dropdownMenu.style.display = 'none';
            } else {
                dropdownMenu.style.display = 'block';
            }
        }

        document.addEventListener('click', function(event) {
            const dropdownMenu = document.getElementById('dropdown-menu');
            const userInfo = document.querySelector('.user-info');

            if (!userInfo.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });
    </script>
</body>

</html>
