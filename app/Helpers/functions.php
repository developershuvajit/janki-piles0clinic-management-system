<?php
declare(strict_types=1);

/**
 * Get the full site URL for a given path.
 */
function site_url(string $path = ''): string
{
    $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/clinic/public', '/');
    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Get the URL of a public asset.
 */
function asset(string $path = ''): string
{
    return site_url('assets/' . ltrim($path, '/'));
}

/**
 * Escapes HTML output to prevent XSS injection.
 */
function esc(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Render a template view.
 * If layout is used, the controller can render a layout and inject the view.
 */
function view(string $viewPath, array $data = []): void
{
    $file = VIEWS_PATH . '/' . str_replace('.', '/', $viewPath) . '.php';
    if (!file_exists($file)) {
        throw new \Exception("View template not found: " . $viewPath);
    }
    
    // Extract variables to the scope of this view template
    extract($data);
    
    // Include the template
    include $file;
}

/**
 * Redirect to a specific site path.
 */
function redirect(string $path): void
{
    header('Location: ' . site_url($path));
    exit();
}

/**
 * Return a JSON response and terminate.
 */
function jsonResponse(mixed $data, int $statusCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Retrieve the active CSRF token.
 */
function csrf_token(): string
{
    return \App\Helpers\Security::generateCsrfToken();
}

/**
 * Generate a CSRF input field.
 */
function csrf_field(): string
{
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . esc($token) . '">';
}

/**
 * Retrieve old input form data after validation fails.
 */
function old(string $key, string $default = ''): string
{
    return \App\Helpers\Session::getFlash('old_' . $key) ?? $default;
}
