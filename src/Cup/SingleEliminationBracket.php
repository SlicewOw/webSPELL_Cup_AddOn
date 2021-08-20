<?php

namespace myrisk\Cup;

use myrisk\Cup\BracketRound;


class SingleEliminationBracket {

    /**
     * @var array<BracketRound> $bracket_rounds
     */
    private $bracket_rounds = array();

    public function addBracketRound(BracketRound $bracket_round): void
    {
        array_push(
            $this->bracket_rounds,
            $bracket_round
        );
    }

    /**
     * @return array<BracketRound>
     */
    public function getBracketRounds(): array
    {
        return $this->bracket_rounds;
    }

}
