<?php

namespace myrisk\Cup;

use \myrisk\Cup\Enum\MatchEnums;

class Match {

    private int $match_id;
    private string $match_format = MatchEnums::CUP_FORMAT_BEST_OF_ONE;
    private \DateTime $match_date;

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