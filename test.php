<?php

use App\Controller\MatchScraper;

require __DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$matchScraper = new MatchScraper();
$url = "https://www.piratichomutov.cz/zapas.asp?sezona=2026";

// var_dump($matchScraper->getMatches($url, true, true));
var_dump($matchScraper->getMatches($url, true, false));
// var_dump($matchScraper->getMatches($url, false, false));
