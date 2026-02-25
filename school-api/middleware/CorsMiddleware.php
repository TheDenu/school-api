<?php

/**
 * Middleware для настройки CORS и безопасности HTTP заголовков
 * Защищает от XSS, Clickjacking и других веб-уязвимостей
 */
class CorsMiddleware
{
    /**
     * Устанавливает все необходимые HTTP заголовки безопасности и CORS
     * Вызывается в начале каждого запроса
     */
    public function handle()
    {
        // === СТАНДАРТНЫЕ ЗАГОЛОВКИ БЕЗОПАСНОСТИ ===
        header('X-Content-Type-Options: nosniff');                    // Запрещаем MIME sniffing
        header('X-Frame-Options: DENY');                             // Запрещаем встраивание в iframe
        header('X-XSS-Protection: 1; mode=block');                   // Включаем XSS защиту браузера
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');  // HSTS на год
        header('Referrer-Policy: strict-origin-when-cross-origin');  // Контролируем referrer

        // === CORS ЗАГОЛОВКИ ДЛЯ DEV ===
        header("Access-Control-Allow-Origin: *");                    // Разрешаем запросы с любого домена
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");  // Разрешенные методы
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept");  // Разрешенные заголовки

        // === CORS ДЛЯ ПРОДАКШЕНА (раскомментировать) ===
        /*
        header("Access-Control-Allow-Origin: https://module.b");     // Только доверенный домен
        header("Access-Control-Allow-Credentials: true");            // Разрешаем cookies и Authorization
        */

        // === ОБРАБОТКА PREFLIGHT ЗАПРОСОВ ===
        // Браузер отправляет OPTIONS перед кросс-доменными POST/PUT/DELETE
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
