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
            $awayMatches = isset($_POST["awayMatches"]);
            $homeMatches = isset($_POST["homeMatches"]);
            $currentMatches = isset($_POST["currentMatches"]);

            $scraper = new MatchScraper();
            $url = "https://www.piratichomutov.cz/zapas.asp?sezona=2026";

            $matches = $scraper->getMatches($url, $awayMatches, $homeMatches);


            $service = new Calendar($this->googleClient->getClient());

            foreach ($matches as $match) {
                $event = new Event([
                    'summary' => $match->getSummary(),
                    'start' => new EventDateTime([
                        'dateTime' => $match->getStartDateTime(),
                        'timeZone' => 'Europe/Prague'
                    ]),
                    'end' => new EventDateTime([
                        'dateTime' => $match->getEndDateTime(),
                        'timeZone' => 'Europe/Prague'
                    ]),
                ]);

                $calendarId = 'primary';
                $event = $service->events->insert($calendarId, $event);
            }
        }
    }
}
