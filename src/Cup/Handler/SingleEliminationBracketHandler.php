<?php

namespace myrisk\Cup\Handler;

use myrisk\Cup\BracketRound;
use myrisk\Cup\Cup;
use myrisk\Cup\SingleEliminationBracket;
use myrisk\Cup\Utils\CupUtils;


class SingleEliminationBracketHandler {

    public static function getBracketOfCup(Cup $cup): SingleEliminationBracket
    {

        $bracket = new SingleEliminationBracket();

        for ($round = 1; $round < $cup->getTotalRoundCount() + 1; $round++) {

            $bracket_round = new BracketRound($round, true);
            $bracket_round->setMatches(
                CupMatchHandler::getMatchesByParameters($cup, $round, true)
            );

            $bracket->addBracketRound($bracket_round);

        }

        return $bracket;

    }

    public static function saveBracket(Cup $cup): SingleEliminationBracket
    {

        $checked_in_participants_in_random_order = CupUtils::getParticipantsInRandomOrder(
            $cup->getSize(),
            $cup->getCheckedInCupParticipants()
        );

        $bracket = new SingleEliminationBracket();

        for ($round = 1; $round < $cup->getTotalRoundCount() + 1; $round++) {

            $participants_of_round = ($round == 1) ? $checked_in_participants_in_random_order : array();

            $bracket->addBracketRound(
                CupMatchHandler::createMatchesOfCup($cup, $round, true, $participants_of_round)
            );

        }

        return self::getBracketOfCup($cup);

    }

}
