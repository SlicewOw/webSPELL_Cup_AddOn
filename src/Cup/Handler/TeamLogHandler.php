<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Team;
use myrisk\Cup\TeamLog;

class TeamLogHandler {

    private const DB_TABLE_NAME_CUPS_TEAMS_LOG = "cups_teams_log";

    /**
     * @return array<TeamLog>
     */
    public static function getTeamLogsByTeam(Team $team): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_TEAMS_LOG)
            ->where('teamID = ?')
            ->setParameter(0, $team->getTeamId())
            ->orderBy("date", "ASC");

        $logs_query = $queryBuilder->execute();

        $team_logs = array();

        while ($logs_result = $logs_query->fetch(FetchMode::MIXED))
        {

            $team_log = new TeamLog();
            $team_log->setTeamName($logs_result['teamName']);
            $team_log->setInfo($logs_result['action']);
            $team_log->setParentId($logs_result['parent_id']);
            $team_log->setDate(
                DateUtils::getDateTimeByMktimeValue((int) $logs_result['date'])
            );

            if (!is_null($logs_result['kicked_id'])) {
                $team_log->setKickedByUser(
                    UserHandler::getUserByUserId((int) $logs_result['kicked_id'])
                );
            }

            array_push(
                $team_logs,
                $team_log
            );
        }

        return $team_logs;

    }

    public static function saveTeamLog(Team $cup_team, TeamLog $log): void
    {

        $kicked_by_user_id = (!is_null($log->getKickedByUser())) ? $log->getKickedByUser()->getUserId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_TEAMS_LOG)
            ->values(
                    [
                        'teamID' => '?',
                        'teamName' => '?',
                        'date' => '?',
                        'kicked_id' => '?',
                        'parent_id' => '?',
                        'action' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup_team->getTeamId(),
                        1 => $cup_team->getName(),
                        2 => $log->getDate()->getTimestamp(),
                        3 => $kicked_by_user_id,
                        4 => $log->getParentId(),
                        5 => $log->getInfo()
                    ]
                );

        $queryBuilder->execute();

    }

}
