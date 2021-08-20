<?php

namespace myrisk\Cup;

use myrisk\Cup\CupMatch;


class BracketRound {

    /**
     * @var int $round_identifier
     */
    private $round_identifier = 1;

    /**
     * @var bool $is_winner_bracket
     */
    private $is_winner_bracket = true;

    /**
     * @var array<CupMatch> $matches
     */
    private $matches = array();

    public function __construct(int $round_identifier, bool $is_winner_bracket)
    {
        $this->round_identifier = $round_identifier;
        $this->is_winner_bracket = $is_winner_bracket;
    }

    public function getRoundIdentifier(): int
    {
        return $this->round_identifier;
    }

    public function isWinnerBracket(): bool
    {
        return $this->is_winner_bracket;
    }

    public function addMatch(CupMatch $cup_match): void
    {
        array_push(
            $this->matches,
            $cup_match
        );
    }

    /**
     * @param array<CupMatch> $cup_matches
     */
    public function setMatches(array $cup_matches): void
    {
        $this->matches = $cup_matches;
    }

    /**
     * @return array<CupMatch>
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

}