<?php
class Router
{
    private array $routes = [];
    private $mysqli;
    private $controllers = [];

    private function getInputData(): array
    {
        return match ($_SERVER['REQUEST_METHOD']) {
            'GET', 'DELETE' => $_GET,
            default => json_decode(file_get_contents('php://input'), true) ?: []
        };
    }

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
        $this->initControllers();
    }

    private function initControllers()
    {
        require_once 'controller/UserController.php';
        require_once 'controller/CourseController.php';
        require_once 'controller/OrderController.php';
        require_once 'service/JwtService.php';
        require_once 'middleware/AuthMiddleware.php';

        $jwtService = new JwtService();

        $this->controllers = [
            'user' => new UserController($this->mysqli, $jwtService),
            'courses' => new CourseController($this->mysqli),
            'orders' => new OrderController($this->mysqli)
        ];
    }

    public function addRoute($method, $path, $controllerKey, $methodName, $middleware = 'auth')
    {
        $this->routes[$method][$path] = [
            'controller' => $this->controllers[$controllerKey],
            'method' => $methodName,
            'middleware' => $middleware
        ];
    }

    public function dispatch($method, $uri)
    {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        $array = explode('/', $path);

        if (count($array) === 1) {
            $path = $array[0];
        } elseif (count($array) === 2) {
            $path = $array[0];
            $_GET['id'] = $array[1];
        } else {
            $path = $array[0] . '/' . $array[2];
            $_GET['id_course'] = $array[1];
            //$path = parse_url($path, PHP_URL_PATH);
        }
        
        $input = $this->getInputData();
        $route = $this->routes[$method][$path] ?? null;

        if (!$route) {
            http_response_code(404);
            echo json_encode(['message' => 'Not found']);
            exit();
        }

        if ($route['middleware'] === 'auth') {
            $auth = new AuthMiddleware($this->mysqli);
            $auth->handle(function () use ($route, $input) {
                $route['controller']->{$route['method']}($input);
            });
        } else {
            $route['controller']->{$route['method']}($input);
        }
    }
}
