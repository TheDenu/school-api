<?php
session_start();
$errors = [];

if (isset($_SESSION['admin'])) {
    header('Location: courses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) $errors['email'] = 'Введите email';
    if (empty($password)) $errors['password'] = 'Введите пароль';

    if (!$errors) {
        if ($email === 'admin@edu.com' && $password === 'course2025') {
            $_SESSION['admin'] = true;
            header('Location: courses.php');
            exit;
        } else {
            $errors['error'] = 'Неверный логин или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - Вход</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <style>
        body {
            height: 100vh;
        }
    </style>
</head>

<body>
    <div
        class="container d-flex justify-content-center align-items-center h-100">
        <div class="col-md-7 col-lg-4">
            <div class="card mt-5 shadow">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">Вход в админ панель</h3>
                    <form action="" method="POST">
                        <small style="color: #e70d0d"><?= $errors['error'] ?? ''  ?></small>

                        <div class="mb-3">
                            <label for="email" class="label-form">email:</label>
                            <input
                                type="text"
                                class="form-control"
                                name="email"
                                id="email"
                             />
                            <small style="color: #e70d0d"><?= $errors['email'] ?? ''  ?></small>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="label-form">пароль:</label>
                            <input
                                type="password"
                                class="form-control"
                                name="password"
                                id="password"
                             />
                            <small style="color: #e70d0d"><?= $errors['password'] ?? ''  ?></small>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button
                                type="submit"
                                class="btn btn-primary">
                                Войти
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>