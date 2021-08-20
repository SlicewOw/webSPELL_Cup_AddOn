<?php

namespace myrisk\Cup\Handler;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\BracketRound;
use myrisk\Cup\Cup;
use myrisk\Cup\CupMatch;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;

class CupMatchHandler {

    private const DB_TABLE_NAME_CUP_MATCHES = "cups_matches_playoff";

    public static function getMatchByMatchId(Cup $cup, int $match_id): CupMatch
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUP_MATCHES)
            ->where('matchID = ?')
            ->setParameter(0, $match_id);

        $cup_match_query = $queryBuilder->executeQuery();
        $cup_match = $cup_match_query->fetchAssociative();

        if (empty($cup_match)) {
            throw new \UnexpectedValueException("unknown_cup_match");
        }

        $match = new CupMatch();
        $match->setMatchId((int) $cup_match["matchID"]);
        $match->setFormat($cup_match["format"]);
        $match->setDate(
            new \DateTime($cup_match["date"])
        );
        $match->setMaps(
            unserialize($cup_match["maps"])
        );
        $match->setIsActive(
            ($cup_match["active"] == 1)
        );
        $match->setLeftTeamConfirmed(
            ($cup_match["team1_confirmed"] == 1)
        );
        $match->setRightTeamConfirmed(
            ($cup_match["team2_confirmed"] == 1)
        );
        $match->setAdminConfirmed(
            ($cup_match["admin_confirmed"] == 1)
        );

        if ($cup->isTeamCup()) {

            if (!is_null($cup_match["team1"])) {
                $match->setLeftTeam(
                    TeamHandler::getTeamByTeamId((int) $cup_match["team1"])
                );
            }

            if (!is_null($cup_match["team2"])) {
                $match->setRightTeam(
                    TeamHandler::getTeamByTeamId((int) $cup_match["team2"])
                );
            }

        } else {

            if (!is_null($cup_match["team1"])) {
                $match->setLeftTeam(
                    UserHandler::getUserByUserId((int) $cup_match["team1"])
                );
            }

            if (!is_null($cup_match["team2"])) {
                $match->setRightTeam(
                    UserHandler::getUserByUserId((int) $cup_match["team2"])
                );
            }

        }

        return $match;

    }

    /**
     * @return array<CupMatch>
     */
    public static function getMatchesByParameters(Cup $cup, int $round, bool $is_winner_bracket): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('matchID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUP_MATCHES)
            ->where('cupID = ?', 'wb = ?', 'runde = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $is_winner_bracket ? 1 : 0)
            ->setParameter(2, $round)
            ->orderBy("spiel", "ASC");

        $cup_match_query = $queryBuilder->executeQuery();
        $cup_matches = $cup_match_query->fetchAllAssociative();

        $matches = array();

        foreach ($cup_matches as $cup_match) {

            array_push(
                $matches,
                self::getMatchByMatchId($cup, (int) $cup_match['matchID'])
            );

        }

        return $matches;

    }

    /**
     * @param array<TeamParticipant|UserParticipant|int> $participants_of_bracket_round
     */
    public static function createMatchesOfCup(Cup $cup, int $round, bool $is_winner_bracket, array $participants_of_bracket_round): BracketRound
    {

        $participant_index = 0;

        $bracket_round = new BracketRound($round, $is_winner_bracket);

        $count_of_matches = $cup->getSize() / (2 * $round);
        for ($match_indentifier = 1; $match_indentifier < $count_of_matches + 1; $match_indentifier++) {

            $match_datetime = clone $cup->getStartDateTime();
            if ($round > 1) {
                $match_datetime->add(
                    new \DateInterval("PT" . ($round - 1) . "H")
                );
            }
            $match_datetime->add(
                new \DateInterval("PT15M")
            );

            $new_match = new CupMatch();
            $new_match->setMatchIdentifier($match_indentifier);
            // TODO: Implement logic to set format per round instead of global format
            $new_match->setFormat(CupEnums::CUP_FORMAT_BEST_OF_ONE);
            $new_match->setDate($match_datetime);
            if (isset($participants_of_bracket_round[$participant_index]) && !is_numeric($participants_of_bracket_round[$participant_index])) {
                if (is_a($participants_of_bracket_round[$participant_index], TeamParticipant::class)) {
                    $new_match->setLeftTeam($participants_of_bracket_round[$participant_index]->getTeam());
                } else {
                    $new_match->setLeftTeam($participants_of_bracket_round[$participant_index]->getUser());
                }
            }
            if (isset($participants_of_bracket_round[$participant_index + 1]) && !is_numeric($participants_of_bracket_round[$participant_index + 1])) {
                if (is_a($participants_of_bracket_round[$participant_index + 1], TeamParticipant::class)) {
                    $new_match->setRightTeam($participants_of_bracket_round[$participant_index + 1]->getTeam());
                } else {
                    $new_match->setRightTeam($participants_of_bracket_round[$participant_index + 1]->getUser());
                }
            }

            if ($round == 1) {
                $new_match->setIsActive(true);
            }

            $saved_match = self::saveMatch($cup, $bracket_round, $new_match);

            $bracket_round->addMatch($saved_match);

            $participant_index += 2;

            if (($round == $cup->getTotalRoundCount()) && !$cup->isThirdPlacementSet()) {
                $match_indentifier = $count_of_matches + 10;
            }

        }

        return $bracket_round;

    }

    public static function saveMatch(Cup $cup, BracketRound $bracket_round, CupMatch $match): CupMatch
    {

        if (is_null($match->getMatchId())) {
            $match = self::insertMatch($cup, $bracket_round, $match);
        } else {
            self::updateMatch($cup, $bracket_round, $match);
        }

        if (is_null($match->getMatchId())) {
            throw new \UnexpectedValueException("match_id_is_invalid");
        }

        return self::getMatchByMatchId($cup, $match->getMatchId());

    }

    private static function insertMatch(Cup $cup, BracketRound $bracket_round, CupMatch $match): CupMatch
    {

        $left_team_walkover = ($bracket_round->getRoundIdentifier() == 1 && is_null($match->getLeftTeam())) ? 1 : 0;

        $left_team_id = null;
        if (!is_null($match->getLeftTeam())) {
            $left_team_id = $cup->isTeamCup() ? $match->getLeftTeam()->getTeamId() : $match->getLeftTeam()->getUserId();
        }

        $right_team_walkover = ($bracket_round->getRoundIdentifier() == 1 && is_null($match->getRightTeam())) ? 1 : 0;

        $right_team_id = null;
        if (!is_null($match->getRightTeam())) {
            $right_team_id = $cup->isTeamCup() ? $match->getRightTeam()->getTeamId() : $match->getRightTeam()->getUserId();
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUP_MATCHES)
            ->values(
                    [
                        'cupID' => '?',
                        'wb' => '?',
                        'runde' => '?',
                        'spiel' => '?',
                        'format' => '?',
                        'date' => '?',
                        'team1' => '?',
                        'team1_freilos' => '?',
                        'team2' => '?',
                        'team2_freilos' => '?',
                        'active' => '?',
                        'team1_confirmed' => '?',
                        'team2_confirmed' => '?',
                        'admin_confirmed' => '?',
                        'maps' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getCupId(),
                        1 => $bracket_round->isWinnerBracket() ? 1 : 0,
                        2 => $bracket_round->getRoundIdentifier(),
                        3 => $match->getMatchIdentifier(),
                        4 => $match->getFormat(),
                        5 => $match->getDate()->format("Y-m-d H:i:s"),
                        6 => $left_team_id,
                        7 => $left_team_walkover,
                        8 => $right_team_id,
                        9 => $right_team_walkover,
                        10 => $match->isActive() ? 1 : 0,
                        11 => $match->isConfirmedByLeftTeam() ? 1 : 0,
                        12 => $match->isConfirmedByRightTeam() ? 1 : 0,
                        13 => $match->isConfirmedByAdmin() ? 1 : 0,
                        14 => serialize($match->getMaps())
                    ]
                );

        $queryBuilder->executeQuery();

        $match->setMatchId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $match;

    }

    private static function updateMatch(Cup $cup, BracketRound $bracket_round, CupMatch $match): void
    {

        $left_team_walkover = ($bracket_round->getRoundIdentifier() == 1 && is_null($match->getLeftTeam())) ? 1 : 0;

        $left_team_id = null;
        if (!is_null($match->getLeftTeam())) {
            $left_team_id = $cup->isTeamCup() ? $match->getLeftTeam()->getTeamId() : $match->getLeftTeam()->getUserId();
        }

        $right_team_walkover = ($bracket_round->getRoundIdentifier() == 1 && is_null($match->getRightTeam())) ? 1 : 0;

        $right_team_id = null;
        if (!is_null($match->getRightTeam())) {
            $right_team_id = $cup->isTeamCup() ? $match->getRightTeam()->getTeamId() : $match->getRightTeam()->getUserId();
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUP_MATCHES)
            ->set("cupID", "?")
            ->set("wb", "?")
            ->set("runde", "?")
            ->set("spiel", "?")
            ->set("format", "?")
            ->set("team1", "?")
            ->set("team1_freilos", "?")
            ->set("team2", "?")
            ->set("team2_freilos", "?")
            ->set("active", "?")
            ->set("team1_confirmed", "?")
            ->set("team2_confirmed", "?")
            ->set("admin_confirmed", "?")
            ->set("maps", "?")
            ->where('matchID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $bracket_round->isWinnerBracket() ? 1 : 0)
            ->setParameter(2, $bracket_round->getRoundIdentifier())
            ->setParameter(3, $match->getMatchIdentifier())
            ->setParameter(4, $match->getFormat())
            ->setParameter(5, $left_team_id)
            ->setParameter(6, $left_team_walkover)
            ->setParameter(7, $right_team_id)
            ->setParameter(8, $right_team_walkover)
            ->setParameter(8, $match->isActive() ? 1 : 0)
            ->setParameter(8, $match->isConfirmedByLeftTeam() ? 1 : 0)
            ->setParameter(8, $match->isConfirmedByRightTeam() ? 1 : 0)
            ->setParameter(8, $match->isConfirmedByAdmin() ? 1 : 0)
            ->setParameter(8, serialize($match->getMaps()));

        $queryBuilder->executeQuery();

    }

}
