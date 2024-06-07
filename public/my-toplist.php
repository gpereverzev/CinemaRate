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

$toplist = Toplist::getUserToplist($user->getUserId());
$toplistId = $toplist['list_id']; // Отримуємо ідентифікатор топ-списку

$films = Toplist::getToplist($toplistId); // Отримуємо список фільмів для даного топ-списку

// Перевіряємо, чи отримані необхідні дані через POST-запит
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['film_id'], $_POST['new_position'])) {
        // Отримуємо film_id та new_position з POST-даних
        $film_id = $_POST['film_id'];
        $new_position = $_POST['new_position'];

        // Викликаємо метод класу ListItem для оновлення позиції фільму в базі даних
        if (ListItem::updateFilmPosition($film_id, $new_position)) {
            // Якщо оновлення успішне, відправляємо відповідь сервера з кодом 200 та повідомленням
            http_response_code(200);
            echo json_encode(['message' => 'Позиція фільму оновлена успішно.']);
        } else {
            // Якщо виникла помилка, відправляємо відповідь сервера з кодом 500 та повідомленням про помилку
            http_response_code(500);
            echo json_encode(['message' => 'Помилка при оновленні позиції фільму.']);
        }
        exit; // Після відповіді сервера вийдемо з файлу, щоб не виводити HTML-розмітку
    }
    if (isset($_POST['film_id'], $_POST['action']) && $_POST['action'] === 'delete') {
        // Отримуємо film_id з POST-даних
        $film_id = $_POST['film_id'];
    
        // Викликаємо метод класу ListItem для видалення фільму з бази даних
        if (ListItem::deleteFilm($film_id)) {
            // Якщо видалення успішне, відправляємо відповідь сервера з кодом 200 та повідомленням
            http_response_code(200);
            echo json_encode(['message' => 'Фільм успішно видалено.']);
        } else {
            // Якщо виникла помилка, відправляємо відповідь сервера з кодом 500 та повідомленням про помилку
            http_response_code(500);
            echo json_encode(['message' => 'Помилка при видаленні фільму.']);
        }
        exit; // Після відповіді сервера вийдемо з файлу, щоб не виводити HTML-розмітку
    }
}

?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Мій топ-список</title>
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

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .toplist-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            margin: 50px auto;
        }

        .toplist-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            /* Додано відступ знизу для меню інструментів */
        }

        .toplist-table th,
        .toplist-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .toplist-table th {
            background-color: #f2f2f2;
        }

        .toplist-table td {
            text-align: center;
        }

        .toplist-table th:nth-child(1),
        .toplist-table td:nth-child(1) {
            width: 20%;
        }

        .toplist-table th:nth-child(2),
        .toplist-table td:nth-child(2) {
            width: 60%;
        }

        .toplist-table th:nth-child(3),
        .toplist-table td:nth-child(3) {
            width: 20%;
        }

        .tool-menu {
            display: flex;
            justify-content: center;
        }

        .tool-menu button {
            margin: 0 5px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        .tool-menu button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo">
            <img src="/src/Logo.svg" alt="Логотип">
        </div>
        <h1>Мій топлист</h1>
        <div class="user-info" onclick="toggleDropdown()">
            <?php if ($user) : ?>
                <img src="../backend/image.php?user_id=<?= $user->getUserId() ?>" alt="<?= $user->getUsername() ?>">
                <div class="nickname"><?= htmlspecialchars($user->getUsername()) ?></div>
            <?php endif; ?>
        </div>
    </header>

    <div class="content">
        <div class="toplist-card">
            <table id="sortable-table" class="toplist-table">
                <thead>
                    <tr>
                        <th>Позиція в топі</th>
                        <th>Назва фільму</th>
                        <th>Дії</th> <!-- Новий стовпець для кнопок редагування -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Вивід результату запиту у вигляді таблиці
                    if ($films) {
                        foreach ($films as $film) {
                            echo '<tr data-id="' . $film['film_id'] . '">';
                            echo '<td>' . htmlspecialchars($film['position']) . '</td>';
                            echo '<td>' . htmlspecialchars($film['title']) . '</td>';
                            echo '<td class="actions">';
                            echo '<div class="tool-menu">';
                            echo '<button onclick="changePosition(' . $film['film_id'] . ', \'up\')">↑</button>';
                            echo '<button onclick="changePosition(' . $film['film_id'] . ', \'down\')">↓</button>';
                            echo '<button onclick="deleteFilm(' . $film['film_id'] . ')">X</button>'; // Додано кнопку видалення
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3">У вас поки немає фільмів у топ-списку.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function changePosition(filmId, direction) {
            var currentPosition = parseInt(document.querySelector('tr[data-id="' + filmId + '"]').cells[0].innerText);
            var newPosition;
            if (direction === 'up') {
                newPosition = currentPosition - 1;
            } else {
                newPosition = currentPosition + 1;
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            };
            xhr.send('film_id=' + filmId + '&new_position=' + newPosition);
        }

        function deleteFilm(filmId) {
            if (confirm('Ви впевнені, що хочете видалити цей фільм?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        console.log(xhr.responseText);
                        // Оновлюємо сторінку після успішного видалення
                        location.reload();
                    }
                };
                xhr.send('film_id=' + filmId + '&action=delete');
            }
        }
    </script>
</body>

</html>