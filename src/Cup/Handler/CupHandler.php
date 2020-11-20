<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\GameHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Admin;
use myrisk\Cup\Cup;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Handler\CupSponsorHandler;

class CupHandler {

    public static function getCupByCupId(int $cup_id): Cup
    {

        if (!Validator::numericVal()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException('cup_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups')
            ->where('cupID = ?')
            ->setParameter(0, $cup_id);

        $cup_query = $queryBuilder->execute();
        $cup_result = $cup_query->fetch(FetchMode::MIXED);

        if (!$cup_result || count($cup_result) < 1) {
            throw new \InvalidArgumentException('unknown_cup');
        }

        $cup = new Cup();
        $cup->setCupId($cup_result['cupID']);
        $cup->setName($cup_result['name']);
        $cup->setMode($cup_result['mode']);
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
        $cup = self::getCupParticipantsOfCup($cup);
        $cup = self::getAdminsOfCup($cup);

        return $cup;

    }

    private static function getCupParticipantsOfCup(Cup $cup): Cup
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

    private static function getAdminsOfCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_admin')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $admin_query = $queryBuilder->execute();

        while ($admin_result = $admin_query->fetch(FetchMode::MIXED)) {

            $admin = new Admin();
            $admin->setAdminId($admin_result['adminID']);
            $admin->setRight($admin_result['rights']);
            $admin->setUser(
                UserHandler::getUserByUserId($admin_result['userID'])
            );

            $cup->addAdmin($admin);

        }

        return $cup;

    }

    public static function saveCup(Cup $cup): Cup
    {

        if (is_null($cup->getGame())) {
            throw new \InvalidArgumentException('game_of_cup_is_not_set_yet');
        }

        if (is_null($cup->getRule())) {
            throw new \InvalidArgumentException('rule_of_cup_is_not_set_yet');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . 'cups')
            ->values(
                    [
                        'name' => '?',
                        'checkin_date' => '?',
                        'start_date' => '?',
                        'mode' => '?',
                        'status' => '?',
                        'game' => '?',
                        'gameID' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getName(),
                        1 => $cup->getCheckInDateTime()->getTimestamp(),
                        2 => $cup->getStartDateTime()->getTimestamp(),
                        3 => $cup->getMode(),
                        4 => $cup->getStatus(),
                        5 => $cup->getGame()->getTag(),
                        6 => $cup->getGame()->getGameId()
                    ]
                );

        $queryBuilder->execute();

        $cup->setCupId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $cup;

    }

}
