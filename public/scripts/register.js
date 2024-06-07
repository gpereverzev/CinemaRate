document.getElementById('registerForm').addEventListener('submit', function (event) {
    event.preventDefault();
  
    const login = document.getElementById('login').value;
    const password = document.getElementById('password').value;
    const username = document.getElementById('username').value;
    const avatar = document.getElementById('avatar').files[0];
  
    const reader = new FileReader();
    reader.readAsDataURL(avatar);
    reader.onload = function () {
      const avatarData = reader.result;
  
      fetch('http://localhost:8000/backend/index.php?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ login, password, username, avatar: avatarData })
      })
        .then(response => response.json())
        .then(data => {
          console.log(data); // Додано для відстеження відповідей від сервера
          window.location.href = 'http://localhost:8000/public/index.php'
          if (data.error) {
              document.getElementById('loginErrorMessage').textContent = data.error;
          } else {
              if (data.redirect) {
                  window.location.href = data.redirect;
              }
          }
      });
    };
  });
  