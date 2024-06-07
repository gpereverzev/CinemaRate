<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cinema Rate</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Register</h2>
            <form id="registerForm">
                <div class="input-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" required>
                </div>
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" required>
                </div>
                <div class="input-group">
                    <label for="avatar" class="file-label">
                        <div id="image-preview" class="avatar-preview"></div>
                        Choose Avatar
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="file-input" onchange="previewImage()">
                </div>
                <button type="submit">Register</button>
            </form>
            <p id="registerErrorMessage" class="error"></p>
            <p id="registerSuccessMessage" class="success"></p>
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>
    <script src="scripts/register.js"></script>
    <script>
function previewImage() {
    const fileInput = document.getElementById('avatar');
    const imagePreview = document.getElementById('image-preview');

    // Отримуємо обраний файл
    const file = fileInput.files[0];

    // Створюємо об'єкт FileReader
    const reader = new FileReader();

    // Прослуховуємо подію завантаження файлу
    reader.onload = function() {
        // Створюємо елемент зображення та встановлюємо йому src зчитаного зображення
        const imgElement = document.createElement('img');
        imgElement.src = reader.result;

        // Додаємо зображення до контейнера попереднього перегляду
        imagePreview.innerHTML = '';
        imagePreview.appendChild(imgElement);
    }

    // Зчитуємо вміст обраного файлу у форматі Data URL
    reader.readAsDataURL(file);
}

    </script>
</body>
</html>
