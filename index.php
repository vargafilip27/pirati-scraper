<?php

use App\Controller\AuthController;
use App\Controller\CalendarController;
use App\Model\GoogleClient;
use Dotenv\Dotenv;

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

session_start();

$dotenv = Dotenv::createImmutable(__DIR__);

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$client = new GoogleClient();

// routing
switch ($uri) {
    case "/":
        header("Location: /add-event");
        break;

    case '/login':
        $controller = new AuthController($client);
        $controller->login();
        break;

    case '/callback':
        $controller = new AuthController($client);
        $controller->callback();
        break;

    case '/add-event':
        $controller = new CalendarController($client);

        // POST - add an event
        // GET - show form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->createEvent();
        else $controller->showEventForm();
        break;

    case '/logout':
        $controller = new AuthController($client);
        $controller->logout();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
