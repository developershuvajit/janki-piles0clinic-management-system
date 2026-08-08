<?php
declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Compile routes using regular expression patterns.
     */
    private function addRoute(string $method, string $path, string $handler): void
    {
        // Convert route parameters (e.g., {id}) to named regex groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        
        // Ensure exact start and end matches
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * Dispatch incoming request to matching controller action.
     */
    public function dispatch(string $uri, string $method): void
    {
        // Strip query strings
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        // Normalize trailing slashes (except root)
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Resolve handler format: ControllerName@methodName
                list($controllerName, $action) = explode('@', $route['handler']);
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (!class_exists($controllerClass)) {
                    throw new \Exception("Routing failure: Controller class '{$controllerClass}' does not exist.");
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $action)) {
                    throw new \Exception("Routing failure: Action method '{$action}' does not exist in '{$controllerClass}'.");
                }

                // Execute action with route parameters
                call_user_func_array([$controller, $action], [$params]);
                return;
            }
        }

        // Return 404 Page
        http_response_code(404);
        $notFoundPage = VIEWS_PATH . '/errors/404.php';
        if (file_exists($notFoundPage)) {
            include $notFoundPage;
        } else {
            echo "<h1>404 Not Found</h1>";
            echo "<p>The requested URL was not found on this server.</p>";
        }
    }
}
