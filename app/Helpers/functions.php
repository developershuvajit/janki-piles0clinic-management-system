<?php
declare(strict_types=1);

/**
 * Get the full site URL for a given path.
 */
function site_url(string $path = ''): string
{
    $baseUrl = rtrim(
        $_ENV['APP_URL'] ?? 'http://localhost/clinic/public',
        '/'
    );

    return $baseUrl . '/' . ltrim($path, '/');
}


/**
 * Get the URL of a public asset.
 */
function asset(string $path = ''): string
{
    return site_url(
        'assets/' . ltrim($path, '/')
    );
}


/**
 * Escapes HTML output to prevent XSS injection.
 */
function esc(?string $value): string
{
    if ($value === null) {
        return '';
    }

    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


/**
 * Render a template view.
 *
 * If layout is used, the controller can render
 * a layout and inject the view.
 */
function view(
    string $viewPath,
    array $data = []
): void {

    $file = VIEWS_PATH . '/' .
        str_replace('.', '/', $viewPath) .
        '.php';

    if (!file_exists($file)) {
        throw new \Exception(
            "View template not found: " . $viewPath
        );
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
    header(
        'Location: ' . site_url($path)
    );

    exit();
}


/**
 * Return a JSON response and terminate.
 */
function jsonResponse(
    mixed $data,
    int $statusCode = 200
): void {

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    http_response_code($statusCode);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

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

    return '<input type="hidden" name="csrf_token" value="' .
        esc($token) .
        '">';
}


/**
 * Retrieve old input form data after validation fails.
 */
function old(
    string $key,
    string $default = ''
): string {

    return \App\Helpers\Session::getFlash(
        'old_' . $key
    ) ?? $default;
}


/*
|--------------------------------------------------------------------------
| GLOBAL SUCCESS SOUND
|--------------------------------------------------------------------------
|
| Automatically plays:
|
| public/assets/sounds/success.mp3
|
| whenever:
|
| Session::setFlash('success', '...');
|
| is used anywhere in the application.
|
| NO CONTROLLER CHANGES REQUIRED.
|
*/


/**
 * Start session if it has not already been started.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/**
 * Start output buffering.
 *
 * This allows us to automatically inject
 * the success sound into the final HTML page.
 */
if (
    !defined('GLOBAL_FLASH_SOUND_BUFFER_STARTED')
) {

    define(
        'GLOBAL_FLASH_SOUND_BUFFER_STARTED',
        true
    );

    ob_start(
        function (string $html): string {

            /*
            |--------------------------------------------------------------------------
            | Check Success Flash
            |--------------------------------------------------------------------------
            */

            $hasSuccessFlash = false;

            if (
                isset($_SESSION['_flash']) &&
                is_array($_SESSION['_flash']) &&
                isset($_SESSION['_flash']['success']) &&
                !empty($_SESSION['_flash']['success'])
            ) {
                $hasSuccessFlash = true;
            }


            /*
            |--------------------------------------------------------------------------
            | No Success Flash
            |--------------------------------------------------------------------------
            |
            | Return page normally.
            |
            */

            if (!$hasSuccessFlash) {
                return $html;
            }


            /*
            |--------------------------------------------------------------------------
            | Success Sound URL
            |--------------------------------------------------------------------------
            */

            $soundUrl = site_url(
                'assets/sounds/success.mp3'
            );


            /*
            |--------------------------------------------------------------------------
            | Escape URL
            |--------------------------------------------------------------------------
            */

            $safeSoundUrl = htmlspecialchars(
                $soundUrl,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );


            /*
            |--------------------------------------------------------------------------
            | Audio + JavaScript
            |--------------------------------------------------------------------------
            */

            $soundScript = <<<HTML

<audio
    id="globalSuccessSound"
    preload="auto"
    style="display:none;"
>
    <source
        src="{$safeSoundUrl}"
        type="audio/mpeg"
    >
</audio>

<script>
(function () {

    function playSuccessSound() {

        const audio =
            document.getElementById(
                'globalSuccessSound'
            );

        if (!audio) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Set volume
        |--------------------------------------------------------------------------
        */

        audio.volume = 0.6;


        /*
        |--------------------------------------------------------------------------
        | Try to play immediately
        |--------------------------------------------------------------------------
        */

        const playPromise = audio.play();


        /*
        |--------------------------------------------------------------------------
        | Browser Autoplay Protection
        |--------------------------------------------------------------------------
        */

        if (
            playPromise !== undefined
        ) {

            playPromise.catch(function () {

                /*
                | Chrome / Edge may block
                | autoplay.
                |
                | Try again after user interaction.
                */

                const retryPlay =
                    function () {

                        audio
                            .play()
                            .catch(
                                function () {}
                            );

                        document
                            .removeEventListener(
                                'click',
                                retryPlay
                            );

                        document
                            .removeEventListener(
                                'keydown',
                                retryPlay
                            );

                        document
                            .removeEventListener(
                                'touchstart',
                                retryPlay
                            );

                    };


                /*
                | Mouse click
                */

                document.addEventListener(
                    'click',
                    retryPlay,
                    {
                        once: true
                    }
                );


                /*
                | Keyboard
                */

                document.addEventListener(
                    'keydown',
                    retryPlay,
                    {
                        once: true
                    }
                );


                /*
                | Mobile touch
                */

                document.addEventListener(
                    'touchstart',
                    retryPlay,
                    {
                        once: true
                    }
                );

            });
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DOM Ready
    |--------------------------------------------------------------------------
    */

    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            playSuccessSound
        );

    } else {

        playSuccessSound();

    }

})();
</script>

HTML;


            /*
            |--------------------------------------------------------------------------
            | Inject Before </body>
            |--------------------------------------------------------------------------
            */

            if (
                stripos(
                    $html,
                    '</body>'
                ) !== false
            ) {

                $html = preg_replace(
                    '/<\/body>/i',
                    $soundScript . '</body>',
                    $html,
                    1
                );

            } else {

                /*
                | If </body> doesn't exist,
                | append to the page.
                */

                $html .= $soundScript;
            }


            /*
            |--------------------------------------------------------------------------
            | Return Modified HTML
            |--------------------------------------------------------------------------
            */

            return $html;
        }
    );
}