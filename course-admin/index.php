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
    $error = curl_error($ch);

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: white;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: white;
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 12px 0 0 12px;
            font-size: 1.1rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-left: none;
            padding: 0.75rem 1rem;
            border-radius: 0 12px 12px 0;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: white;
            border-color: #4dabf7;
            box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.1);
        }

        .error-field {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
            background: #ffe6e6;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #4dabf7 0%, #3b82f6 100%);
            border: none;
            color: white;
            padding: 0.875rem;
            width: 100%;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(77, 171, 247, 0.4);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
        }
    </style>
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