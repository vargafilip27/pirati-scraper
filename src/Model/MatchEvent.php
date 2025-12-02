<?php

namespace App\Model;

use DateTime;

class MatchEvent {
    private string $summary;
    private DateTime $startDateTime;
    private DateTime $endDateTime;

    public function __construct(string $summary, DateTime $startDateTime) {
        $this->summary = $summary;

        $this->startDateTime = $startDateTime;

        $this->endDateTime = clone $startDateTime;
        $this->endDateTime->modify("+2 hours");
    }
}
