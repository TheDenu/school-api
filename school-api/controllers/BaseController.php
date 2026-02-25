<?php

/**
 * Базовый класс для всех контроллеров REST API
 * Унифицирует отправку HTTP ответов с правильными статус-кодами
 */
class BaseController
{
    /**
     * Универсальный метод отправки JSON ответа
     * @param mixed $data Данные для клиента (массив, объект, строка)
     * @param int $statusCode HTTP статус код (200, 201, 400, 404...)
     */
    protected function sendResponse($data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * ✅ Успешный ответ (200 OK)
     * Для GET запросов и успешных операций
     */
    protected function sendSuccess($data)
    {
        $this->sendResponse($data, 200);
    }

    /**
     * ✅ Создан ресурс (201 Created)
     * Для POST запросов при создании
     */
    protected function sendCreate($data)
    {
        $this->sendResponse($data, 201);
    }

    /**
     * ✅ Нет содержимого (204 No Content)
     * Для DELETE успешного удаления
     */
    protected function sendNoContent()
    {
        http_response_code(204);
        exit();
    }

    /**
     * ❌ Ошибка запроса (400 Bad Request)
     * Неверный синтаксис, отсутствующие обязательные поля
     */
    protected function sendBadRequest($msg = "Bad Request")
    {
        $this->sendResponse(['message' => $msg], 400);
    }

    /**
     * ❌ Ресурс не найден (404 Not Found)
     */
    protected function sendNotFound($msg = "Not Found")
    {
        $this->sendResponse(['message' => $msg], 404);
    }

    /**
     * ❌ Не авторизован (401 Unauthorized)
     * Нет токена JWT или истек
     */
    protected function sendUnauthorized($msg = "Unauthorized")
    {
        $this->sendResponse(['message' => $msg], 401);
    }

    /**
     * ❌ Доступ запрещен (403 Forbidden)
     * Токен валиден, но нет прав на операцию
     */
    protected function sendForbidden($msg = "Forbidden for you")
    {
        $this->sendResponse(['message' => $msg], 403);
    }

    /**
     * ❌ Ошибка валидации (422 Unprocessable Entity)
     * Данные прошли парсинг, но не прошли бизнес-валидацию
     */
    protected function sendValidationErrors($errors, string $msg = "Invalid fields")
    {
        $this->sendResponse([
            'message' => $msg,
            'errors' => $errors
        ], 422);
    }

    /**
     * ❌ Внутренняя ошибка сервера (500 Internal Server Error)
     * Исключения БД, непредвиденные ошибки
     * Public для вызова из catch блоков
     */
    public function sendServerError($msg = "Internal Server Error")
    {
        $this->sendResponse(['message' => $msg], 500);
    }
}
