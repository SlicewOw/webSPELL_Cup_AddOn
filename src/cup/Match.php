<?php

namespace myrisk\Cup;

use \myrisk\Cup\Enum\MatchEnums;

class Match {

    /** @var int $match_id */
    private $match_id;

    /** @var string $match_format */
    private $match_format = MatchEnums::CUP_FORMAT_BEST_OF_ONE;

    /** @var \DateTime $match_date */
    private $match_date;

    public function setMatchId(int $match_id): void
    {
        $this->match_id = $match_id;
    }

    public function getMatchId(): ?int
    {
        return $this->match_id;
    }

    public function setMatchFormat(string $match_format): void
    {
        $this->match_format = $match_format;
    }

    public function getMatchFormat(): ?string
    {
        return $this->match_format;
    }

    public function setMatchDate(\DateTime $match_date): void
    {
        $this->match_date = $match_date;
    }

    public function getMatchDate(): \DateTime
    {
        return $this->match_date;
    }

}