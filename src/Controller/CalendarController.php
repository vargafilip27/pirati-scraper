<?php

namespace App\Controller;

use App\Model\GoogleClient;
use Exception;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class CalendarController {
    private $googleClient;

    public function __construct(GoogleClient $googleClient) {
        $this->googleClient = $googleClient;
    }

    public function createEvent() {
        if (!$this->googleClient->isLoggedIn()) {
            header("Location: /pirati");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $awayMatches = isset($_POST["awayMatches"]);
            $homeMatches = isset($_POST["homeMatches"]);

            $scraper = new MatchScraper();
            $url = "https://www.hokej.cz/maxa-liga/zapasy?t=1xbr%3C%21doctype+html+public&matchList-filter-season=2025&matchList-filter-competition=7415&matchList-filter-team=822";

            $matches = $scraper->getMatches($url, $homeMatches, $awayMatches);

            $service = new Calendar($this->googleClient->getClient());
            $calendarId = "primary";

            foreach ($matches as $match) {
                $matchId = md5($match->getSummary() . $match->getStartDateTime());

                $event = new Event([
                    'id' => $matchId,
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

                if ($match->isHomeMatch()) {
                    $event->setLocation("ROCKNET ARÉNA, Mostecká 5773, 430 01 Chomutov");
                }

                try {
                    $service->events->insert($calendarId, $event);
                }
                catch (Exception $e) {
                    if ($e->getCode() == 409) {}    // Duplicate event
                    else echo "Error adding event: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
}
