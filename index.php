<?php

use App\Controller\AuthController;
use App\Controller\CalendarController;
use App\Model\GoogleClient;
use Dotenv\Dotenv;

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

session_start();

$dotenv = Dotenv::createImmutable(__DIR__);

$basePath = '/pirati';
$requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$route = str_replace($basePath, '', $requestUri);

// Ensure the route is not empty (if visiting /pirati, route might be empty string)
if ($route === '') $route = '/';

$client = new GoogleClient();

// routing
switch ($route) {
    case "/":
        header("Location: /pirati/add-event");
        break;

    case '/login':
        $authController = new AuthController($client);
        $authController->login();
        break;

    case '/callback':
        $authController = new AuthController($client);
        $authController->callback();
        break;

    case '/add-event':
        $calendarController = new CalendarController($client);

        // POST - add an event
        // GET - show form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $calendarController->createEvent();
        else $calendarController->showEventForm();
        break;

    case '/logout':
        $authController = new AuthController($client);
        $authController->logout();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
