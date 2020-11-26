<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use \webspell_ng\WebSpellDatabaseConnection;
use \webspell_ng\Handler\GameHandler;
use \webspell_ng\Handler\UserHandler;
use \webspell_ng\Utils\DateUtils;

use \myrisk\Cup\Cup;
use \myrisk\Cup\TeamParticipant;
use \myrisk\Cup\UserParticipant;
use \myrisk\Cup\Enum\CupEnums;
use \myrisk\Cup\Handler\AdminHandler;
use \myrisk\Cup\Handler\CupSponsorHandler;
use \myrisk\Cup\Handler\TeamHandler;

class CupHandler {

    private const DB_TABLE_NAME_CUPS = "cups";

    public static function getCupByCupId(int $cup_id): Cup
    {

        if (!Validator::numericVal()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException('cup_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS)
            ->where('cupID = ?')
            ->setParameter(0, $cup_id);

        $cup_query = $queryBuilder->execute();
        $cup_result = $cup_query->fetch(FetchMode::MIXED);

        if (empty($cup_result)) {
            throw new \InvalidArgumentException('unknown_cup');
        }

        $cup = new Cup();
        $cup->setCupId($cup_result['cupID']);
        $cup->setName($cup_result['name']);
        $cup->setMode($cup_result['mode']);
        $cup->setSize($cup_result['max_size']);
        $cup->setStatus($cup_result['status']);
        $cup->setCheckInDateTime(DateUtils::getDateTimeByMktimeValue($cup_result['checkin_date']));
        $cup->setStartDateTime(DateUtils::getDateTimeByMktimeValue($cup_result['start_date']));

        $rule_id = $cup_result['ruleID'];
        if ($rule_id > 0) {
            $cup->setRule(
                RuleHandler::getRuleByRuleId($rule_id)
            );
        }

        $game_id = $cup_result['gameID'];
        if ($game_id > 0) {
            $cup->setGame(
                GameHandler::getGameByGameId((int) $game_id)
            );
        }

        $cup = CupSponsorHandler::getSponsorsOfCup($cup);
        $cup = self::setCupParticipantsOfCup($cup);
        return self::setAdminsOfCup($cup);

    }

    private static function setCupParticipantsOfCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_participants')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $cup_participant_query = $queryBuilder->execute();

        while ($cup_participant_result = $cup_participant_query->fetch(FetchMode::MIXED)) {

            if ($cup->getMode() == CupEnums::CUP_MODE_1ON1) {
                $cup = self::addUserParticipantToCup($cup, $cup_participant_result);
            } else {
                $cup = self::addTeamParticipantToCup($cup, $cup_participant_result);
            }

        }

        return $cup;

    }

    /**
     * @param array<mixed> $user_participant
     */
    private static function addUserParticipantToCup(Cup $cup, array $user_participant): Cup
    {

        $user_particpant = new UserParticipant();
        $user_particpant->setParticipantId($user_participant['ID']);
        $user_particpant->setCheckedIn($user_participant['checked_in']);
        $user_particpant->setRegisterDateTime(DateUtils::getDateTimeByMktimeValue($user_participant['date_register']));

        $date_checking = $user_participant['date_checkin'];
        if (Validator::numericVal()->min(1)->validate($date_checking)) {
            $user_particpant->setCheckInDateTime(DateUtils::getDateTimeByMktimeValue($date_checking));
        }

        $user_particpant->setUser(UserHandler::getUserByUserId($user_participant['teamID']));

        $cup->addCupParticipant($user_particpant);

        return $cup;

    }

    /**
     * @param array<mixed> $team_participant
     */
    private static function addTeamParticipantToCup(Cup $cup, array $team_participant): Cup
    {

        $team_particpant = new TeamParticipant();
        $team_particpant->setParticipantId($team_participant['ID']);
        $team_particpant->setCheckedIn($team_participant['checked_in']);
        $team_particpant->setRegisterDateTime(DateUtils::getDateTimeByMktimeValue($team_participant['date_register']));

        $date_checking = $team_participant['date_checkin'];
        if (Validator::numericVal()->min(1)->validate($date_checking)) {
            $team_particpant->setCheckInDateTime(DateUtils::getDateTimeByMktimeValue($date_checking));
        }

        $team_particpant->setTeam(TeamHandler::getTeamByTeamId($team_participant['teamID']));

        $cup->addCupParticipant($team_particpant);

        return $cup;

    }

    private static function setAdminsOfCup(Cup $cup): Cup
    {

        $admins = AdminHandler::getAdminsOfCup($cup);
        foreach ($admins as $admin) {
            $cup->addAdmin($admin);
        }

        return $cup;

    }

    public static function saveCup(Cup $cup): Cup
    {

        if (is_null($cup->getRule())) {
            throw new \InvalidArgumentException('rule_of_cup_is_not_set_yet');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS)
            ->values(
                    [
                        'name' => '?',
                        'checkin_date' => '?',
                        'start_date' => '?',
                        'mode' => '?',
                        'max_size' => '?',
                        'status' => '?',
                        'game' => '?',
                        'gameID' => '?',
                        'ruleID' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getName(),
                        1 => $cup->getCheckInDateTime()->getTimestamp(),
                        2 => $cup->getStartDateTime()->getTimestamp(),
                        3 => $cup->getMode(),
                        4 => $cup->getSize(),
                        5 => $cup->getStatus(),
                        6 => $cup->getGame()->getTag(),
                        7 => $cup->getGame()->getGameId(),
                        8 => (!is_null($cup->getRule())) ? $cup->getRule()->getRuleId() : null
                    ]
                );

        $queryBuilder->execute();

        $cup->setCupId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $cup;

    }

    public static function updateCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS)
            ->set("name", "?")
            ->set("checkin_date", "?")
            ->set("start_date", "?")
            ->set("mode", "?")
            ->set("max_size", "?")
            ->set("status", "?")
            ->set("game", "?")
            ->set("gameID", "?")
            ->set("ruleID", "?")
            ->where('cupID = ?')
            ->setParameter(0, $cup->getName())
            ->setParameter(1, $cup->getCheckInDateTime()->getTimestamp())
            ->setParameter(2, $cup->getStartDateTime()->getTimestamp())
            ->setParameter(3, $cup->getMode())
            ->setParameter(4, $cup->getSize())
            ->setParameter(5, $cup->getStatus())
            ->setParameter(6, $cup->getGame()->getTag())
            ->setParameter(7, $cup->getGame()->getGameId())
            ->setParameter(8, $cup->getRule()->getRuleId())
            ->setParameter(9, $cup->getCupId());

        return self::getCupByCupId($cup->getCupId());

    }

}
