<?php
// Устанавливаем часовой пояс Москвы для корректной работы с датой и временем
date_default_timezone_set('Europe/Moscow');

// Устанавливаем часовой пояс Москвы для корректной работы с датой и временем
session_start();

require_once 'service/DBConnect.php';
require_once 'middleware/ChainMiddleware.php';
require_once 'Router.php';

// Пытаемся установить подключение к базе данных
try {
    $mysqli = getDBConnection();
} catch (Exception $e) {
    exit();
}

// Создаем экземпляр роутера, передавая ему подключение к БД
$router = new Router($mysqli);

// Регистрируем маршруты API:
// Формат: метод, путь, контроллер, метод_контроллера, middleware (auth - требует авторизации)
$router->addRoute('POST', 'registr', 'user', 'registration', null);
$router->addRoute('POST', 'auth', 'user', 'authorization', null);
$router->addRoute('GET', 'logout', 'user', 'logout', 'auth');
$router->addRoute('GET', 'courses', 'courses', 'coursesHandler', 'auth');
$router->addRoute('POST', 'courses/buy', 'orders', 'order', 'auth');
$router->addRoute('POST', 'payment-webhook', 'orders', 'payment', null);
$router->addRoute('GET', 'orders', 'orders', 'orders', 'auth');
$router->addRoute('DELETE', 'orders', 'orders', 'deleteOrder', 'auth');

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// === МIDDLEWARE ЦЕПОЧКА ===
ChainMiddleware::handle(function () use ($router, $method, $uri) {
    $router->dispatch($method, $uri);
});
