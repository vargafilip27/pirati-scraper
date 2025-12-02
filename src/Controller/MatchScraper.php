<?php

namespace App\Controller;

use App\Model\MatchEvent;
use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class MatchScraper {
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => 'https://www.piratichomutov.cz',
            'timeout'  => 10.0,

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

        if ($homeMatches) {
            $crawler->filter(".doma")->each(function (Crawler $profile) use (&$matches) {
                // doma - teams - names - long (e.g. Piráti Chomutov - SC Marimex Kolín)
                $summary = $profile->filter(".long")->text();

                // doma - teams - info (e.g. 29.kolo, so 6.12.2025)
                $date = $profile->filter(".info")->text();

                // Convert czech date format into DateTime
                if (preg_match('/(\d{1,2}\.\d{1,2}\.\d{4})/', $date, $matching)) {
                    $dateString = $matching[1];     // This becomes "6.12.2025"
                    $dateObject = DateTime::createFromFormat("j.n.Y", $dateString);

                    $now = new DateTime();  // Current date

                    if ($dateObject > $now) {
                        $time = $profile->filter(".score")->text();

                        $dateTimeObject = DateTime::createFromFormat("j.n.Y H:i", $dateString . " " . $time);

                        // Create new match and add it into results
                        $matches[] = new MatchEvent($summary, $dateTimeObject);
                    }
                }
            });
        }

        if ($awayMatches) {
            $crawler->filter(".venku")->each(function (Crawler $profile) use (&$matches) {
                // venku - teams - names - long (e.g. VHK ROBE Vsetín - Piráti Chomutov)
                $summary = $profile->filter(".long")->text();

                // venku - teams - info (e.g. 30.kolo, st 10.12.2025)
                $date = $profile->filter(".info")->text();

                // Convert czech date format into DateTime
                if (preg_match('/(\d{1,2}\.\d{1,2}\.\d{4})/', $date, $matching)) {
                    $dateString = $matching[1];     // This becomes "10.12.2025"
                    $dateObject = DateTime::createFromFormat("j.n.Y", $dateString);

                    $now = new DateTime();  // Current date

                    if ($dateObject > $now) {
                        $time = $profile->filter(".score")->text();

                        $dateTimeObject = DateTime::createFromFormat("j.n.Y H:i", $dateString . " " . $time);

                        // Create new match and add it into results
                        $matches[] = new MatchEvent($summary, $dateTimeObject);
                    }
                }
            });
        }

        return $matches;
    }
}
