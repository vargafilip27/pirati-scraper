<?php

namespace App\Model;

use DateTime;

class Match {
    private string $summary;
    private DateTime $startDateTime;
    private DateTime $endDateTime;

    public function __construct($summary, $startDateTimeString, $endDateTimeString) {
        $this->summary = $summary;
        // TODO
    }
}
