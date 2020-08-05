<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;

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

        $cup = SponsorHandler::getSponsorByCup($cup);
        $cup = CupHandler::getCupParticipantsOfCup($cup);

        return $cup;

    }

    private static function getCupParticipantsOfCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_teilnehmer')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $cup_participant_query = $queryBuilder->execute();

        while ($cup_participant_result = $cup_participant_query->fetch(FetchMode::MIXED)) {

            if ($cup->getMode() == CupEnums::CUP_MODE_1ON1) {
                $cup = CupHandler::addUserParticipantToCup($cup, $cup_participant_result);
            } else {
                $cup = CupHandler::addTeamParticipantToCup($cup, $cup_participant_result);
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

}
