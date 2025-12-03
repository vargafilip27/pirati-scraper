<?php

use App\Controller\MatchScraper;

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$matchScraper = new MatchScraper();
$url = "https://www.hokej.cz/maxa-liga/zapasy?t=1xbr%3C%21doctype+html+public&matchList-filter-season=2025&matchList-filter-competition=7415&matchList-filter-team=822";

// var_dump($matchScraper->getMatches($url, true, true));
// var_dump($matchScraper->getMatches($url, true, false));
var_dump($matchScraper->getMatches($url, false, true));
// var_dump($matchScraper->getMatches($url, false, false));
