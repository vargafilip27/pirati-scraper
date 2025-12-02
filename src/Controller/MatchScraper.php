<?php

namespace App\Controller;

use App\Model\Match;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class MatchScraper {
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'timeout'  => 5.0,
            // Mimic a real browser to avoid being blocked
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
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
                if (preg_match('/(\d{1,2}\.\d{1,2}\.\d{4})/', $date, $matches)) {
                    $cleanDateString = $matches[1]; // This becomes "6.12.2025"

                    $time = $profile->filter(".score")->text();
                    $dateObject = \DateTime::createFromFormat('j.n.Y H:i', $cleanDateString . " " . $time);

                    // Create new match and add it into results
                    $matches[] = new Match($summary, $dateObject);
                }
            });
        }

        if ($awayMatches) {
            $crawler->filter(".venku")->each(function (Crawler $profile) use (&$matches) {
                $summary = $profile->filter(".long")->text();
                $date = $profile->filter(".info")->text();
                $time =

                $matches[] = new Match($summary, $date);
            });
        }

        return $matches;
    }
}
