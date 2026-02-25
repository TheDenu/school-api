<?php

/**
 * Контроллер для управления пользователями
 * Регистрация, авторизация, выход из системы
 */
require_once 'BaseController.php';

class UserController extends BaseController
{
    /** @var mysqli Подключение к базе данных */
    protected $mysqli;

    /** @var JwtService Сервис для работы с JWT токенами */
    protected $jwtService;

    /**
     * Конструктор - DI для БД и JWT сервиса
     */
    public function __construct($mysqli, $jwtService)
    {
        $this->mysqli = $mysqli;
        $this->jwtService = $jwtService;
    }

    /**
     * Регистрация нового пользователя
     * POST /school-api/registr
     * @param array $input JSON данные: {full_name, email, password}
     */
    public function registration(array $input)
    {
        // === ВАЛИДАЦИЯ ВХОДНЫХ ДАННЫХ ===
        $errors = [];

        // Проверка email
        if (!empty($input['email'])) {
            if (!filter_var($input['email'], FILTER_SANITIZE_EMAIL)) {
                $errors['email'] = 'введите корректный email';
            }
        } else {
            $errors['email'] = 'email обязателен';
        }

        // Сложный пароль: a-z, A-Z, 0-9, спецсимволы
        if (!empty($input['password'])) {
            $passwordErrors = '';
            if (!preg_match('/[a-z]/', $input['password'])) {
                $passwordErrors .= 'строчная буква; ';
            }
            if (!preg_match('/[A-Z]/', $input['password'])) {
                $passwordErrors .= 'заглавная буква; ';
            }
            if (!preg_match('/\d/', $input['password'])) {
                $passwordErrors .= 'цифра; ';
            }
            if (!preg_match('/[!@#$%^&*()-_+=]/', $input['password'])) {
                $passwordErrors .= 'спецсимвол (!@#$%^&*()-_+=); ';
            }
            if ($passwordErrors) {
                $errors['password'] = rtrim($passwordErrors, '; ');
            }
        } else {
            $errors['password'] = 'password обязателен';
        }

        if (empty($input['full_name'])) {
            $errors['full_name'] = 'full_name обязателен';
        }

        // Отправляем 422 если есть ошибки валидации
        if (!empty($errors)) {
            $this->sendValidationErrors($errors);
            return;
        }

        // === ПРОВЕРКА УНИКАЛЬНОСТИ EMAIL ===
        $stmt = $this->mysqli->prepare("SELECT 1 FROM users WHERE email = ?");
        $stmt->bind_param("s", $input['email']);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $this->sendBadRequest('Пользователь с такой почтой уже зарегистрирован');
            return;
        }

        // === СОЗДАНИЕ ПОЛЬЗОВАТЕЛЯ ===
        $hash = password_hash($input['password'], PASSWORD_BCRYPT);

        $stmt = $this->mysqli->prepare("
            INSERT INTO users (email, full_name, password) 
            VALUES (?,?,?)
        ");
        $stmt->bind_param("sss", $input['email'], $input['full_name'], $hash);

        if ($stmt->execute()) {
            $this->sendCreate(['success' => true]);
        } else {
            $this->sendServerError('Ошибка создания учетной записи');
        }
    }

    /**
     * Авторизация пользователя
     * POST /school-api/auth
     * Возвращает JWT токен
     * @param array $input {email, password}
     */
    public function authorization(array $input)
    {
        // === БАЗОВАЯ ВАЛИДАЦИЯ ===
        $errors = [];
        if (empty($input['email'])) {
            $errors['email'] = 'email обязателен';
        }
        if (empty($input['password'])) {
            $errors['password'] = 'password обязателен';
        }

        if (!empty($errors)) {
            $this->sendValidationErrors($errors);
            return;
        }

        // === ПОИСК ПОЛЬЗОВАТЕЛЯ ===
        $email = $input['email'];
        $stmt = $this->mysqli->prepare("
            SELECT user_id, full_name, password 
            FROM users 
            WHERE email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$user = $result->fetch_assoc()) {
            $this->sendBadRequest('Неверный email или пароль');
            return;
        }

        // === ПРОВЕРКА ПАРОЛЯ ===
        if (!password_verify($input['password'], $user['password'])) {
            $this->sendBadRequest('Неверный email или пароль');
            return;
        }

        // === ГЕНЕРАЦИЯ JWT ТОКЕНА ===
        $token = $this->jwtService->generateToken([
            'user_id' => $user['user_id'],
            'full_name' => $user['full_name'],
            'email' => $input['email']
        ]);

        $created_at = date('Y-m-d H:i:s');
        $expired_at = date('Y-m-d H:i:s', time() + 3600); // 1 час

        // === СОХРАНЕНИЕ ТОКЕНА В БД ===
        $stmt = $this->mysqli->prepare("
            INSERT INTO user_tokens (user_id, token, created_at, expired_at) 
            VALUES (?,?,?,?)
        ");
        $stmt->bind_param('isss', $user['user_id'], $token, $created_at, $expired_at);

        if ($stmt->execute()) {
            $this->sendSuccess(['token' => $token]);
        } else {
            $this->sendServerError($stmt->error);
        }
    }

    /**
     * Выход из системы (инвалидация токена)
     * POST /school-api/logout
     * Удаляет токен из БД (blacklist)
     */
    public function logout()
    {
        // Получаем токен из заголовка Authorization: Bearer <token>
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
            $this->sendBadRequest('Токен не передан');
            return;
        }

        $token = $matches[1];

        // Удаляем токен из БД
        $stmt = $this->mysqli->prepare("
            DELETE FROM user_tokens 
            WHERE token = ?
        ");
        $stmt->bind_param("s", $token);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $this->sendSuccess(['message' => 'Успешный выход']);
        } else {
            $this->sendBadRequest('Токен не найден');
        }
    }
}
