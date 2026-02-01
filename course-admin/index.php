<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .login-card {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .error-field {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center p-3">
    <div class="card login-card shadow-lg" style="max-width: 420px; width: 100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                <h2 class="fw-bold text-white">Админ-панель</h2>
                <p class="text-white-50">Войдите для управления курсами</p>
            </div>

            <!-- Ошибка авторизации -->
            <div class="alert alert-danger d-none" id="auth-error"></div>

            <form id="loginForm">
                <div class="mb-4">
                    <label class="form-label fw-semibold text-white">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="admin@example.com" required>
                    </div>
                    <div class="error-message d-none" id="email-error"></div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-white">Пароль</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="error-message d-none" id="password-error"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="fas fa-sign-in-alt me-2"></i>Войти
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Клиентская валидация
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.email.value;
            const password = this.password.value;
            let valid = true;

            // Очистка ошибок
            document.querySelectorAll('.error-field, .error-message').forEach(el => {
                el.classList.add('d-none');
                el.classList.remove('error-field');
            });

            if (!email) {
                document.querySelector('[name="email"]').classList.add('error-field');
                document.getElementById('email-error').textContent = 'Email обязателен';
                document.getElementById('email-error').classList.remove('d-none');
                valid = false;
            }

            if (!password) {
                document.querySelector('[name="password"]').classList.add('error-field');
                document.getElementById('password-error').textContent = 'Пароль обязателен';
                document.getElementById('password-error').classList.remove('d-none');
                valid = false;
            }

            if (valid) {
                // AJAX запрос к login.php
                fetch('login.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            email,
                            password
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'courses.php';
                        } else {
                            document.getElementById('auth-error').textContent = data.message;
                            document.getElementById('auth-error').classList.remove('d-none');
                        }
                    });
            }
        });
    </script>
</body>

</html>