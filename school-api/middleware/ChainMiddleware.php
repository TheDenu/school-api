<?php

/**
 * Цепочка middleware - выполняет middleware последовательно
 * Стандартный паттерн (как в Express.js, Laravel)
 */
class ChainMiddleware
{
    /**
     * Выполняет цепочку middleware, передавая управление следующему
     * @param callable $next Финальный обработчик (Router::dispatch)
     */
    public static function handle(callable $next)
    {
        // 1. CORS + Security (обязательно первым!)
        require_once __DIR__ . '/CorsMiddleware.php';
        (new CorsMiddleware())->handle();

        // 2. Rate Limiter (опционально)
        // (new RateLimiter())->check();

        // 3. Logger (опционально)
        // (new RequestLogger())->log($_SERVER['REQUEST_URI']);

        // 4. Передаем управление Router'у
        $next();
    }
}
