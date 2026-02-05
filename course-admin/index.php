<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) $errors['email'] = 'input email';
    if (empty($password)) $errors['password'] = 'input password';

    //$response = makeRequest($email, $password);

    if ($email === 'admin@edu.com' && $password === 'course2025') {
        $_SESSION['admin'] = true;
        header('Location: courses.php');
        exit;
    } else {
        $errors['error'] = 'Неверный логин или пароль';
        //$error = $response['message'] ?? 'Ошибка авторизации';
    }
}
/*
function makeRequest($email, $password)
{
    $ch = curl_init();

    $jsonData = json_encode([
        'email' => $email,
        'password' => $password
    ]);

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://module.b/school-api/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_VERBOSE => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $data = json_decode($response, true) ?: [];

    if ($httpCode === 200 && isset($data['token'])) {
        return [
            'success' => true,
            'token' => $data['token']
        ];
    }

    return [
        'success' => false,
        'message' => $data['message'] ?? 'Нет token в ответе API',
        'http_code' => $httpCode,
        'has_token' => isset($data['token']),
        'response' => $response
    ];
}*/
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Вход</title>
    <link rel="stylesheet" href="./styles/auth.css">
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <h2>Админ-панель</h2>
            <p>Войдите для управления курсами</p>
        </div>

        <form method="POST" action="index.php">
            <small style="color: #e70d0d;"><?= $errors['error'] ?? ''  ?></small>

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">📧</span>
                    <input type="email" class="form-control" name="email" placeholder="admin@example.com" value="<?= htmlspecialchars($email ?? '') ?>">
                    <small style="color: #e70d0d;"><?= $errors['email'] ?? ''  ?></small>

                </div>
                <div class="error-message" id="email-error"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Пароль</label>
                <div class="input-group">
                    <span class="input-group-text">🔒</span>
                    <input type="password" class="form-control" name="password" placeholder="••••••••">
                    <small style="color: #e70d0d;"><?= $errors['password'] ?? ''  ?></small>

                </div>
                <div class="error-message" id="password-error"></div>
            </div>

            <button type="submit" class="btn-login">
                Войти
            </button>
        </form>
    </div>
</body>

</html>