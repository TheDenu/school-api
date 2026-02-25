<?php

/**
 * Основной класс роутера для обработки REST API запросов
 * Поддерживает маршрутизацию, middleware и DI контейнер для контроллеров
 */
class Router
{
    private array $routes = [];        // Массив зарегистрированных маршрутов
    private $mysqli;                   // Подключение к базе данных
    private $controllers = [];         // DI контейнер с экземплярами контроллеров

    /**
     * Получает входные данные запроса в зависимости от HTTP метода
     * @return array Данные из $_GET или JSON тела запроса
     */
    private function getInputData(): array
    {
        return match ($_SERVER['REQUEST_METHOD']) {
            'GET', 'DELETE' => $_GET,
            default => json_decode(file_get_contents('php://input'), true) ?: []
        };
    }

    /**
     * Конструктор - инициализирует подключение к БД и контроллеры
     */
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
        $this->initControllers();
    }

    /**
     * Инициализирует и регистрирует все контроллеры в DI контейнере
     * Подключает необходимые сервисы (JWT, AuthMiddleware)
     */
    private function initControllers()
    {
        // Подключаем контроллеры и сервисы
        require_once 'controllers/UserController.php';
        require_once 'controllers/CourseController.php';
        require_once 'controllers/OrderController.php';
        require_once 'service/JwtService.php';
        require_once 'middleware/AuthMiddleware.php';

        $jwtService = new JwtService();

        // Регистрируем контроллеры в DI контейнере с передачей зависимостей
        $this->controllers = [
            'user' => new UserController($this->mysqli, $jwtService),
            'courses' => new CourseController($this->mysqli),
            'orders' => new OrderController($this->mysqli)
        ];
    }

    /**
     * Регистрирует новый маршрут API
     * @param string $method HTTP метод (GET, POST, DELETE...)
     * @param string $path Путь маршрута (например 'registr', 'courses/buy')
     * @param string $controllerKey Ключ контроллера из DI контейнера
     * @param string $methodName Метод контроллера для обработки
     * @param string|null $middleware Имя middleware ('auth' или null)
     */
    public function addRoute($method, $path, $controllerKey, $methodName, $middleware = 'auth')
    {
        $this->routes[$method][$path] = [
            'controller' => $this->controllers[$controllerKey],
            'method' => $methodName,
            'middleware' => $middleware
        ];
    }

    /**
     * Основной метод диспетчеризации запросов
     * Парсит URI, находит маршрут, применяет middleware и вызывает контроллер
     */
    public function dispatch($method, $uri)
    {
        // Парсим путь из URI (убираем /api/ префикс и ведущие/завершающие слеши)
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        $path = substr($path, 11);

        $array = explode('/', $path);

        if (count($array) === 1) {
            $path = $array[0];
        } elseif (count($array) === 2) {
            $path = $array[0];
            $_GET['id'] = $array[1];
        } else {
            $path = $array[0] . '/' . $array[2];
            $_GET['id'] = $array[1];
        }

        // Получаем входные данные запроса
        $input = $this->getInputData();

        // Ищем подходящий маршрут
        $route = $this->routes[$method][$path] ?? null;

        // 404 если маршрут не найден
        if (!$route) {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit();
        }

        // Применяем middleware авторизации если требуется
        if ($route['middleware'] === 'auth') {
            $auth = new AuthMiddleware($this->mysqli);
            // Передаем колбэк для выполнения после успешной авторизации
            $auth->handle(function () use ($route, $input) {
                $route['controller']->{$route['method']}($input);
            });
        } else {
            // Вызываем контроллер напрямую (для webhook'ов)
            $route['controller']->{$route['method']}($input);
        }
    }
}
