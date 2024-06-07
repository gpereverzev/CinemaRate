<?php
require_once 'E:/СМП/cinemaRate/backend/Toplist.php';
require_once 'E:/СМП/cinemaRate/backend/ListItem.php';
require_once 'E:/СМП/cinemaRate/backend/Film.php';
require_once 'E:/СМП/cinemaRate/backend/Session.php';
require_once 'E:/СМП/cinemaRate/backend/User.php';
require_once 'E:/СМП/cinemaRate/backend/Rate.php';
require_once 'E:/СМП/cinemaRate/backend/Database.php';

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

if (!isset($_GET['film_id'])) {
    echo "Фільм не знайдений.";
    exit;
}

$film_id = $_GET['film_id'];
$film = Film::getFilmById($film_id);

if (!$film) {
    echo "Фільм не знайдений.";
    exit;
}

$title = htmlspecialchars($film['title'] ?? 'Невідомий фільм');
$genre = htmlspecialchars($film['genre'] ?? 'Жанр не вказано');
$director = htmlspecialchars($film['director'] ?? 'Режисер не вказаний');
$description = htmlspecialchars($film['anotation'] ?? 'Опис відсутній');
$actors = htmlspecialchars($film['actors'] ?? 'Актори не вказані');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $user->getUserId();
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? null;

    // Додавання оцінки до бази даних, якщо вона встановлена
    if ($rating !== null) {
        if (Rate::addRate($film_id, $user_id, $rating)) {
            echo "<div class='notification'>Оцінка додана.</div>";
        } else {
            echo "<div class='notification error'>Не вдалося додати оцінку.</div>";
        }
    }

    // Додавання коментаря до бази даних, якщо він встановлений
    if ($comment !== null) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO Coments (user_id, film_id, text) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $film_id, $comment]);
        echo "<div class='notification'>Коментар доданий.</div>";
    }


    // Додавання фільму до топлиста
    if (isset($_POST['add_to_toplist'])) {
        $toplist = Toplist::getUserToplist($user_id);

        if (!$toplist) {
            $list_id = Toplist::createToplist($user_id);
        } else {
            $list_id = $toplist['list_id'];
        }

        $max_position = ListItem::getMaxPosition($list_id);
        ListItem::addFilmToList($list_id, $film_id, $max_position + 1);
    }
}

// Отримання оцінок та коментарів
$ratesAndComments = Rate::getRatesAndCommentsByFilmId($film_id);

if (!is_array($ratesAndComments)) {
    $ratesAndComments = [];
}

$averageRating = '-.-';
$totalRating = 0;
$count = 0;

foreach ($ratesAndComments as $rateAndComment) {
    $totalRating += $rateAndComment['rate'];
    $count++;
}

if ($count > 0) {
    $averageRating = round($totalRating / $count, 1);
}

?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
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
            animation: gradient 10s ease infinite;
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

        .comment img {
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

        .film-container {
            display: flex;
            padding: 20px;
            justify-content: center;
        }

        .film-poster {
            flex: 1;
            max-width: 300px;
            margin-right: 20px;
        }

        .film-poster img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .film-details,
        .film-rating {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .film-details h2,
        .film-rating h2 {
            margin-top: 0;
        }

        .film-rating {
            margin-left: 20px;
            flex: 1;
            max-width: 300px;
        }

        .film-rating form {
            display: flex;
            flex-direction: column;
        }

        .film-rating .stars {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .film-rating .stars input {
            display: none;
        }

        .film-rating .stars label {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
        }

        .film-rating textarea {
            resize: vertical;
            padding: 10px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .film-rating button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .film-rating button:hover {
            background-color: #45a049;
        }

        .comments-container {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .comments-container h2 {
            margin-top: 0;
        }

        .comment {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .comment:last-child {
            border-bottom: none;
        }

        .comment .username {
            font-weight: bold;
        }

        .comment .rating {
            color: gold;
            margin-left: 10px;
        }

        .comment .text {
            margin-top: 5px;
        }

        .average-rating {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 10s ease infinite;
            border: 1px solid #ddd;
            border-radius: 100px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .average-rating .rating-value {
            font-size: 48px;
            color: gold;
        }

        .average-rating .star {
            font-size: 48px;
            color: gold;
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
                <a href="/public/my-toplist.php?user_id=<?= $user->getUserId() ?>">Мій топлист</a>
            </div>
        </div>
    </header>

    <div class="film-container">
        <div class="film-poster">
            <img src="../backend/image.php?film_id=<?= $film_id ?>" alt="<?= $title ?>">
            <div class="average-rating">
                <span class="rating-value"><?= $averageRating ?></span>
                <span class="star">★</span>
            </div>
        </div>
        <div class="film-details">
            <h2><?= $title ?>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="add_to_toplist" value="1">
                    <button type="submit" style="background: none; border: none; cursor: pointer;">
                        <img src="/src/add_icon.png" alt="Додати до топ-листа" style="height: 24px; width: 24px;">
                    </button>
                </form>
            </h2>
            <p><strong>Жанр:</strong> <?= $genre ?></p>
            <p><strong>Режисер:</strong> <?= $director ?></p>
            <p><strong>Опис:</strong> <?= $description ?></p>
            <p><strong>Актори:</strong> <?= $actors ?></p>
        </div>
        <div class="film-rating">
            <h2>Оцініть фільм</h2>
            <form method="post" action="">
                <div class="stars">
                    <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
                    <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
                    <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
                    <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
                    <input type="radio" name="rating" id="star5" value="5"><label for="star5">★</label>
                </div>
                <textarea name="comment" placeholder="Ваш коментар"></textarea>
                <input type="hidden" name="film_id" value="<?= $film_id ?>">
                <button type="submit">Відправити</button>
            </form>
        </div>
    </div>

    <div class="comments-container">
        <h2>Коментарі та оцінки</h2>
        <?php foreach ($ratesAndComments as $row) : ?>
            <div class="comment">
                <img class="avatar" src="../backend/image.php?user_id=<?= htmlspecialchars($row['user_id']) ?>" alt="<?= htmlspecialchars($row['username']) ?>">
                <span class="username"><?= htmlspecialchars($row['username']) ?></span>
                <span class="rating"><?= str_repeat('★', $row['rate']) ?></span>
                <div class="text"><?= htmlspecialchars($row['text']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        const stars = document.querySelectorAll('.stars input[type="radio"]');
        const labels = document.querySelectorAll('.stars label');

        stars.forEach((star, index) => {
            star.addEventListener('mouseover', () => {
                for (let i = 0; i <= index; i++) {
                    labels[i].style.color = 'gold';
                }
            });
            star.addEventListener('mouseout', () => {
                for (let i = 0; i < stars.length; i++) {
                    if (!stars[i].checked) {
                        labels[i].style.color = '#ddd';
                    }
                }
            });
            star.addEventListener('change', () => {
                for (let i = 0; i < stars.length; i++) {
                    if (i <= index) {
                        labels[i].style.color = 'gold';
                    } else {
                        labels[i].style.color = '#ddd';
                    }
                }
            });
        });

        function toggleDropdown() {
            const dropdownMenu = document.getElementById('dropdown-menu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>

</html>