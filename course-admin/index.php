<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $response = makeRequest($email, $password);

    // ✅ Проверяем success И token
    if ($response['success'] && isset($response['token'])) {
        $_SESSION['admin'] = true;
        $_SESSION['token'] = $response['token'];
        header('Location: adminPanel.php');
        exit;
    } else {
        $error = $response['message'] ?? 'Ошибка авторизации';
        error_log("Login failed: " . print_r($response, true));
    }
}

function makeRequest($email, $password)
{
    $ch = curl_init();

    $jsonData = json_encode([
        'email' => $email,
        'password' => $password
    ]);

    curl_setopt_array($ch, [
        CURLOPT_URL => 'http://127.0.0.1:8000/school-api/auth',
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
}
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

        <?php if (isset($response)): ?>
            <div style="background: #f8f9fa; padding: 1rem; margin: 1rem 0; font-family: monospace;">
                <strong>API Debug:</strong><br>
                Success: <?= $response['success'] ? 'YES' : 'NO' ?><br>
                Token: <?= isset($response['token']) ? 'YES' : 'NO' ?><br>
                HTTP: <?= $response['http_code'] ?? 'N/A' ?><br>
                Message: <?= htmlspecialchars($response['message'] ?? 'N/A') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">📧</span>
                    <input type="email" class="form-control" name="email" placeholder="admin@example.com" value="<?= htmlspecialchars($email ?? '') ?>">
                </div>
                <div class="error-message" id="email-error"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Пароль</label>
                <div class="input-group">
                    <span class="input-group-text">🔒</span>
                    <input type="password" class="form-control" name="password" placeholder="••••••••">
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