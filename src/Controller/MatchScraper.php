<?php

namespace App\Controller;

use App\Model\MatchEvent;
use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class MatchScraper {
    private Client $client;
    private $season = [
        0 => 2025,
        1 => 2026
    ];

    public function __construct() {
        $this->client = new Client([
            // Force IPv4
            'force_ip_resolve' => 'v4',

            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    public function getMatches(string $url, bool $homeMatches, bool $awayMatches) {
        $response = $this->client->get($url);
        $html = (string)$response->getBody();

        $crawler = new Crawler($html);
        $matches = [];

        $crawler->filter(".js-preview__link")->each(

            function ($node) use (&$matches, &$homeMatches, &$awayMatches) {
                $homeTeam = $node->filter(".preview__name text right")->filter(".preview__name--long")->text();
                $awayTeam = $node->filter(".preview__name text right")->filter(".preview__name--long")->text();

                $columns = $node->filter('.col-1_3');

                if ($columns->count() >= 3) {
                    $rawDate = trim($columns->eq(1)->text());   // e.g. 06. 12.
                    $rawTime = trim($columns->eq(2)->text());   // e.g. 17:30

                    $date = str_replace(' ', '', $rawDate); // "06.12."

                    $dateParts = explode('.', $date);
                    // $parts[0] is Day (06), $parts[1] is Month (12)

                    if (isset($parts[1])) {
                        $month = (int)$parts[1];

                        // e.g.: July-Dec = 2025, Jan-June = 2026
                        if ($month >= 7) $year = $this->season[0];
                        else $year = $this->season[1];

                        // Format: "06.12.2025 17:30"
                        $fullDateString = "$date$year $rawTime";

                        $dateObject = DateTime::createFromFormat('d.m.Y H:i', $fullDateString);

                        if ($homeMatches && $homeTeam == "Piráti Chomutov" && $dateObject) {
                            $matches[] = new MatchEvent("$homeTeam - $awayTeam", $dateObject);
                        }
                        else if ($awayMatches && $awayTeam == "Piráti Chomutov" && $dateObject) {
                            $matches[] = new MatchEvent("$awayTeam - $homeTeam", $dateObject);
                        }
                    }
                }
            }
        );

        return $matches;
    }
}
