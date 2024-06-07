document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const login = document.getElementById('login').value;
    const password = document.getElementById('password').value;

    fetch('http://localhost:8000/public/index.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ login, password })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Виведе відповідь в консоль для відстеження
        if (data.error) {
            document.getElementById('loginErrorMessage').textContent = data.error;
        } else if (data.success) {
            // Зберегти user_id в localStorage
            localStorage.setItem('user_id', data.user_id);
            // Перенаправлення на сторінку homePage.php
            window.location.href = 'http://localhost:8000/public/homePage.php?user_id=' + data.user_id;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
