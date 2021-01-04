<?php

namespace myrisk\Cup;

use \myrisk\Cup\Enum\MatchEnums;

class CupMatch {

    /**
     * @var int $match_id
     */
    private $match_id;

    /**
     * @var string $format
     */
    private $format = MatchEnums::CUP_FORMAT_BEST_OF_ONE;

    /**
     * @var \DateTime $date
     */
    private $date;

    public function setMatchId(int $match_id): void
    {
        $this->match_id = $match_id;
    }

    public function getMatchId(): ?int
    {
        return $this->match_id;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

}