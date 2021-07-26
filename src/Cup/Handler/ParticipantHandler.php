<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\Result;

use webspell_ng\UserLog;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Handler\UserLogHandler;

use myrisk\Cup\Cup;
use myrisk\Cup\TeamLog;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Handler\TeamLogHandler;
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

        $cup_participant_query = $queryBuilder->executeQuery();

        if ($cup->getMode() == CupEnums::CUP_MODE_1ON1) {
            return self::getUserParticipantsOfCup($cup_participant_query);
        } else {
            return self::getTeamParticipantsOfCup($cup_participant_query);
        }

    }

    /**
     * @return array<UserParticipant>
     */
    private static function getUserParticipantsOfCup(Result $cup_participant_query): array
    {

        $user_participants = array();

        $user_participant_array = $cup_participant_query->fetchAllAssociative();
        foreach ($user_participant_array as $user_participant) {

            $particpant = new UserParticipant();
            $particpant->setParticipantId($user_participant['ID']);
            $particpant->setCheckedIn(
                ($user_participant['checked_in'] == 1)
            );
            $particpant->setRegisterDateTime(
                new \DateTime($user_participant['date_register'])
            );
            $particpant->setUser(
                UserHandler::getUserByUserId((int) $user_participant['teamID'])
            );

            if (!is_null($user_participant['date_checkin'])) {
                $particpant->setCheckInDateTime(
                    new \DateTime($user_participant['date_checkin'])
                );
            }

            array_push(
                $user_participants,
                $particpant
            );

        }

        return $user_participants;

    }

    /**
     * @return array<TeamParticipant>
     */
    private static function getTeamParticipantsOfCup(Result $cup_participant_query): array
    {

        $team_participants = array();

        $team_participant_array = $cup_participant_query->fetchAllAssociative();
        foreach ($team_participant_array as $team_participant) {

            $particpant = new TeamParticipant();
            $particpant->setParticipantId($team_participant['ID']);
            $particpant->setCheckedIn(
                ($team_participant['checked_in'] == 1)
            );
            $particpant->setRegisterDateTime(
                new \DateTime($team_participant['date_register'])
            );
            $particpant->setTeam(
                TeamHandler::getTeamByTeamId((int) $team_participant['teamID'])
            );

            if (!is_null($team_participant['date_checkin'])) {
                $particpant->setCheckInDateTime(
                    new \DateTime($team_participant['date_checkin'])
                );
            }

            array_push(
                $team_participants,
                $particpant
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

        $date_checkin = (!is_null($participant->getCheckInDateTime())) ? $participant->getCheckInDateTime()->format("Y-m-d H:i:s") : null;

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
                        3 => $participant->getRegisterDateTime()->format("Y-m-d H:i:s"),
                        4 => $date_checkin
                    ]
                );

        $queryBuilder->executeQuery();

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

        $date_checkin = (!is_null($participant->getCheckInDateTime())) ? $participant->getCheckInDateTime()->format("Y-m-d H:i:s") : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PARTICIPANTS)
            ->set("cupID", "?")
            ->set("teamID", "?")
            ->set("checked_in", "?")
            ->set("date_register", "?")
            ->set("date_checkin", "?")
            ->where('ID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $team_id)
            ->setParameter(2, $checked_in)
            ->setParameter(3, $participant->getRegisterDateTime()->format("Y-m-d H:i:s"))
            ->setParameter(4, $date_checkin)
            ->setParameter(5, $participant->getParticipantId());

        $queryBuilder->executeQuery();

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

        $queryBuilder->executeQuery();

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

        if (get_class($participant) == "myrisk\Cup\TeamParticipant") {
            self::saveTeamLogJoinCup($cup, $participant);
        } else {
            self::saveUserLogJoinCup($cup, $participant);
        }

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

        if (get_class($participant) == "myrisk\Cup\TeamParticipant") {
            self::saveTeamLogCheckedInCup($cup, $participant);
        } else {
            self::saveUserLogCheckedInCup($cup, $participant);
        }

    }

    /**
     * @param UserParticipant|TeamParticipant $participant
     */
    public static function leaveCup(Cup $cup, $participant): void
    {

        self::removeCupParticipant($cup, $participant);

        if (get_class($participant) == "myrisk\Cup\TeamParticipant") {
            self::saveTeamLogLeftCup($cup, $participant);
        } else {
            self::saveUserLogLeftCup($cup, $participant);
        }

    }

    private static function saveUserLogJoinCup(Cup $cup, UserParticipant $participant): void
    {
        if (!is_null($cup->getCupId())) {
            UserLogHandler::saveUserLog(
                $participant->getUser(),
                self::getUserParticipantUserLog($cup->getCupId(), CupEnums::CUP_PARTICIPANT_JOINED)
            );
        }
    }

    private static function saveTeamLogJoinCup(Cup $cup, TeamParticipant $participant): void
    {
        if (!is_null($cup->getCupId())) {
            TeamLogHandler::saveTeamLog(
                $participant->getTeam(),
                self::getTeamParticipantTeamLog($cup->getCupId(), CupEnums::CUP_PARTICIPANT_JOINED)
            );
        }
    }

    private static function saveUserLogCheckedInCup(Cup $cup, UserParticipant $participant): void
    {
        if (!is_null($cup->getCupId())) {
            UserLogHandler::saveUserLog(
                $participant->getUser(),
                self::getUserParticipantUserLog($cup->getCupId(), CupEnums::CUP_PARTICIPANT_CHECKED_IN)
            );
        }
    }

    private static function saveTeamLogCheckedInCup(Cup $cup, TeamParticipant $participant): void
    {
        if (!is_null($cup->getCupId())) {
            TeamLogHandler::saveTeamLog(
                $participant->getTeam(),
                self::getTeamParticipantTeamLog($cup->getCupId(), CupEnums::CUP_PARTICIPANT_CHECKED_IN)
            );
        }
    }

    private static function saveUserLogLeftCup(Cup $cup, UserParticipant $participant): void
    {
        if (!is_null($cup->getCupId())) {
            UserLogHandler::saveUserLog(
                $participant->getUser(),
                self::getUserParticipantUserLog($cup->getCupId(), CupEnums::CUP_PARTICIPANT_LEFT)
            );
        }
    }

    private static function saveTeamLogLeftCup(Cup $cup, TeamParticipant $participant): void
    {
        if (!is_null($cup->getCupId())) {
            TeamLogHandler::saveTeamLog(
                $participant->getTeam(),
                self::getTeamParticipantTeamLog($cup->getCupId(), CupEnums::CUP_PARTICIPANT_LEFT)
            );
        }
    }

    private static function getUserParticipantUserLog(int $cup_id, string $info): UserLog
    {
        $log = new UserLog();
        $log->setInfo($info);
        $log->setParentId($cup_id);
        return $log;
    }

    private static function getTeamParticipantTeamLog(int $cup_id, string $info): TeamLog
    {
        $log = new TeamLog();
        $log->setInfo($info);
        $log->setParentId($cup_id);
        return $log;
    }

}
