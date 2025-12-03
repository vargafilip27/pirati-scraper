<?php

namespace App\Model;

use DateTime;

class MatchEvent {
    private $summary;
    private $startDateTime;
    private $endDateTime;

    public function __construct(string $summary, DateTime $startDateTime) {
        $this->summary = $summary;

        $this->startDateTime = $startDateTime;

        $this->endDateTime = clone $startDateTime;
        $this->endDateTime->modify("+2 hours");
    }

    public function getSummary() {
        return $this->summary;
    }

    public function getStartDateTime() {
        return $this->startDateTime;
    }

    public function getEndDateTime() {
        return $this->endDateTime;
    }
}
