<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Enum\CupEnums;


class ParticipantHandler {

    private const DB_TABLE_NAME_CUPS_PARTICIPANTS = "cups_participants";

    /**
     * @return array<UserParticipant|TeamParticipant>
     */
    public static function getParticipantsOfCup(Cup $cup): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PARTICIPANTS)
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $cup_participant_query = $queryBuilder->execute();

        if ($cup->getMode() == CupEnums::CUP_MODE_1ON1) {
            return self::getUserParticipantsOfCup($cup_participant_query);
        } else {
            return self::getTeamParticipantsOfCup($cup_participant_query);
        }

    }

    /**
     * @return array<UserParticipant>
     */
    private static function getUserParticipantsOfCup(ResultStatement $cup_participant_query): array
    {

        $user_participants = array();

        while ($user_participant = $cup_participant_query->fetch(FetchMode::MIXED))
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

            array_push(
                $user_participants,
                $user_particpant
            );

        }

        return $user_participants;

    }

    /**
     * @return array<TeamParticipant>
     */
    private static function getTeamParticipantsOfCup(ResultStatement $cup_participant_query): array
    {

        $team_participants = array();

        while ($team_participant = $cup_participant_query->fetch(FetchMode::MIXED))
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

            array_push(
                $team_participants,
                $team_participants
            );

        }

        return $team_participants;

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    private static function saveCupParticipant(Cup $cup, $participant): void
    {

        if (is_null($participant->getParticipantId())) {
            self::insertCupParticipant($cup, $participant);
        } else {
            self::updateCupParticipant($cup, $participant);
        }

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    private static function insertCupParticipant(Cup $cup, $participant): void
    {

        if (get_class($participant) == "myrisk\Cup\TeamParticipant") {
            $team_id = $participant->getTeam()->getTeamId();
        } else {
            $team_id = $participant->getUser()->getUserId();
        }

        $checked_in = $participant->getCheckedIn() ? 1 : 0;

        $date_checkin = is_null($participant->getCheckInDateTime()) ? $participant->getCheckInDateTime()->getTimestamp() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PARTICIPANTS)
            ->values(
                    [
                        'cupID' => '?',
                        'teamID' => '?',
                        'checked_in' => '?',
                        'date_register' => '?',
                        'date_checkin' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getCupId(),
                        1 => $team_id,
                        2 => $checked_in,
                        3 => $participant->getRegisterDateTime()->getTimestamp(),
                        4 => $date_checkin
                    ]
                );

        $queryBuilder->execute();

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    private static function updateCupParticipant(Cup $cup, $participant): void
    {

        if (get_class($participant) == "myrisk\Cup\TeamParticipant") {
            $team_id = $participant->getTeam()->getTeamId();
        } else {
            $team_id = $participant->getUser()->getUserId();
        }

        $checked_in = $participant->getCheckedIn() ? 1 : 0;

        $date_checkin = is_null($participant->getCheckInDateTime()) ? $participant->getCheckInDateTime()->getTimestamp() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PARTICIPANTS)
            ->set("cupID", "?")
            ->set("teamID", "?")
            ->set("checked_in", "?")
            ->set("date_register", "?")
            ->set("date_checkin", "?")
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $team_id)
            ->setParameter(2, $checked_in)
            ->setParameter(3, $participant->getRegisterDateTime()->getTimestamp())
            ->setParameter(4, $date_checkin)
            ->setParameter(5, $participant->getParticipantId());

        $queryBuilder->execute();

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    private static function removeCupParticipant(Cup $cup, $participant): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->delete(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PARTICIPANTS)
            ->where("ID = ?", "cupID = ?")
            ->setParameter(0, $participant->getParticipantId())
            ->setParameter(1, $cup->getCupId());

        $queryBuilder->execute();

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    public static function joinCup(Cup $cup, $participant): void
    {

        $participant->setCheckedIn(false);
        $participant->setRegisterDateTime(
            new \DateTime("now")
        );

        self::saveCupParticipant($cup, $participant);

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    public static function confirmCupParticipation(Cup $cup, $participant): void
    {

        $participant->setCheckedIn(true);
        $participant->setCheckInDateTime(
            new \DateTime("now")
        );

        self::saveCupParticipant($cup, $participant);

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    public static function leaveCup(Cup $cup, $participant): void
    {
        self::removeCupParticipant($cup, $participant);
    }

}
