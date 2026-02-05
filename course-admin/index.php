<?php
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) $errors['email'] = 'input email';
    if (empty($password)) $errors['password'] = 'input password';

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Вход</title>
    <link rel="stylesheet" href="./styles/style.css">
</head>

<body>
    <div class="container">
        <div class="main">
            <h1 class="main-h1">Welcome to admin-panel for courses</h1>
            <form class="main-form" method="POST">
                <p class="main-form__p">Please login</p>
                <small style="color: #e70d0d;"><?= $errors['error'] ?? ''  ?></small>
                <div>
                    <input type="email" name="email" placeholder="email" class="main-form__txt" value="<?= htmlspecialchars($email ?? '') ?>"><br>
                    <small style="color: #e70d0d;"><?= $errors['email'] ?? ''  ?></small>
                </div>
                <div class="main-form-second">
                    <input type="password" name="password" placeholder="password" class="main-form__txt "><br>
                    <small style="color: #e70d0d;"><?= $errors['password'] ?? ''  ?></small>
                </div>
                <input type="submit" value="login" class="main-form__btn">
            </form>
        </div>
    </div>
</body>

</html>