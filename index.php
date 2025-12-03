<?php

use App\Controller\AuthController;
use App\Controller\CalendarController;
use App\Model\GoogleClient;
use Dotenv\Dotenv;

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

session_start();

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$basePath = '/pirati';
$requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$route = str_replace($basePath, '', $requestUri);

// Ensure the route is not empty (if visiting /pirati, route might be empty string)
if ($route === '') $route = '/';

$client = new GoogleClient();

switch ($route) {
    case "/":
    case "":
        // Check login status
        if (!$client->isLoggedIn()) {
            require __DIR__ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "loginForm.html";
        }
        else require __DIR__ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "addEventForm.html";
        break;

    case "/login":
        $authController = new AuthController($client);
        $authController->login();
        break;

    case "/callback":
        $authController = new AuthController($client);
        $authController->callback();
        break;

    case "/add-event":
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $calendarController = new CalendarController($client);
            $calendarController->createEvent();
        }

        header("Location: $basePath/");
        break;

    case "/logout":
        $auth = new AuthController($client);
        $auth->logout();
        break;

    case "/privacy":
        require __DIR__ . '/templates/privacy.html';
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
