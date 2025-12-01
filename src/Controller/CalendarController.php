<?php

namespace App\Controller;

use App\Model\GoogleClient;
use App\View\AddEvent;
use App\View\Login;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class CalendarController {
    private $googleClient;

    public function __construct(GoogleClient $googleClient) {
        $this->googleClient = $googleClient;
    }

    public function showEventForm() {
        if (!$this->googleClient->isLoggedIn()) {
            $login = new Login();
            $login->showLoginForm();
        }
        else {
            $addEvent = new AddEvent();
            $addEvent->showEventForm();
        }
    }

    public function createEvent() {
        if (!$this->googleClient->isLoggedIn()) {
            $login = new Login();
            $login->showLoginForm();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $outerMatches = $_POST["outerMatches"];
            $homeMatches = $_POST["homeMatches"];
            $currentMatches = $_POST["currentMatches"];
            $playoffMatches = $_POST["playoffMatches"];

            $matches = [];

            // scrape matches -- TODO

            $service = new Calendar($this->googleClient->getClient());

            foreach ($matches as $match) {
                $event = new Event([
                    'summary' => $match->getSummary(),
                    'start' => new EventDateTime(['dateTime' => $match->getStartDateTime()]),
                    'end' => new EventDateTime(['dateTime' => $match->getEndDateTime()]),
                ]);

                $calendarId = 'primary';
                $event = $service->events->insert($calendarId, $event);
            }
        }
    }
}
