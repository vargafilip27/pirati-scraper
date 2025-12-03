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

    public function getSummary(): string {
        return $this->summary;
    }

    public function getStartDateTime(): string {
        return $this->startDateTime->format(DateTime::RFC3339);
    }

    public function getEndDateTime(): string {
        return $this->endDateTime->format(DateTime::RFC3339);
    }
}
