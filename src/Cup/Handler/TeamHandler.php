<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\User;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Utils\DateUtils;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Team;
use myrisk\Cup\Handler\TeamMemberHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\UserSession;

class TeamHandler {

    private const DB_TABLE_NAME_TEAMS = "cups_teams";

    public static function getTeamByTeamId(int $team_id): Team
    {

        if (!Validator::numericVal()->min(1)->validate($team_id)) {
            throw new \InvalidArgumentException('team_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAMS)
            ->where('teamID = ?')
            ->setParameter(0, $team_id);

        $team_query = $queryBuilder->execute();
        $team_result = $team_query->fetch();

        if (empty($team_result)) {
            throw new \UnexpectedValueException('unknown_cup_team');
        }

        $team = new Team();
        $team->setTeamId($team_result['teamID']);
        $team->setName($team_result['name']);
        $team->setTag($team_result['tag']);
        $team->setHomepage($team_result['hp']);
        $team->setLogotype($team_result['logotype']);
        $team->setIsDeleted($team_result['deleted']);
        $team->setCreationDate(
            DateUtils::getDateTimeByMktimeValue($team_result['date'])
        );
        $team->setCountry(
            CountryHandler::getCountryByCountryShortcut($team_result['country'])
        );

        $team_members = TeamMemberHandler::getActiveMembersOfTeam($team);
        foreach ($team_members as $team_member) {
            $team->addMember($team_member);
        }

        return $team;

    }

    /**
     * @return array<Team>
     */
    public static function getTeamsOfUser(User $user): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('t.teamID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAMS, "t")
            ->innerJoin('t', WebSpellDatabaseConnection::getTablePrefix() . TeamMemberHandler::DB_TABLE_NAME_TEAM_MEMBERS, 'tm', 't.teamID = tm.teamID')
            ->where('tm.userID = ?', 'tm.active = 1')
            ->setParameter(0, $user->getUserId());

        $team_query = $queryBuilder->execute();

        $teams = array();

        while ($team_result = $team_query->fetch())
        {
            array_push(
                $teams,
                self::getTeamByTeamId((int) $team_result['teamID'])
            );
        }

        return $teams;

    }

    public static function isAnyTeamAdmin(): bool
    {

        if (UserSession::getUserId() < 1) {
            return false;
        }

        $teams_of_user = self::getTeamsOfUser(
            UserHandler::getUserByUserId(UserSession::getUserId())
        );

        foreach ($teams_of_user as $team) {
            if ($team->getTeamAdmin()->getUser()->getUserId() == UserSession::getUserId()) {
                return true;
            }
        }

        return false;

    }

    public static function isAnyTeamMember(): bool
    {

        if (UserSession::getUserId() < 1) {
            return false;
        }

        $teams_of_user = self::getTeamsOfUser(
            UserHandler::getUserByUserId(UserSession::getUserId())
        );

        return !empty($teams_of_user);

    }

    public static function saveTeam(Team $team): Team
    {

        if (is_null($team->getTeamId())) {
            $team = self::insertTeam($team);
            TeamMemberHandler::saveTeamMembers($team);
        } else {
            self::updateTeam($team);
        }

        return TeamHandler::getTeamByTeamId($team->getTeamId());

    }

    private static function insertTeam(Team $team): Team
    {

        $team_admin = $team->getTeamAdmin();
        if (is_null($team_admin)) {
            throw new \InvalidArgumentException("every_cup_team_needs_an_admin");
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAMS)
            ->values(
                    [
                        'date' => '?',
                        'name' => '?',
                        'tag' => '?',
                        'userID' => '?',
                        'country' => '?',
                        'hp' => '?',
                        'logotype' => '?',
                        'password' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $team->getCreationDate()->getTimestamp(),
                        1 => $team->getName(),
                        2 => $team->getTag(),
                        3 => $team_admin->getUser()->getUserId(),
                        4 => $team->getCountry()->getShortcut(),
                        5 => $team->getHomepage(),
                        6 => $team->getLogotype(),
                        7 => StringFormatterUtils::getRandomString(20)
                    ]
                );

        $queryBuilder->execute();

        $team->setTeamId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $team;

    }

    private static function updateTeam(Team $team): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAMS)
            ->set("date", "?")
            ->set("name", "?")
            ->set("tag", "?")
            ->set("country", "?")
            ->set("hp", "?")
            ->set("logotype", "?")
            ->set("password", "?")
            ->where('teamID = ?')
            ->setParameter(0, $team->getCreationDate()->getTimestamp())
            ->setParameter(1, $team->getName())
            ->setParameter(2, $team->getTag())
            ->setParameter(3, $team->getCountry()->getShortcut())
            ->setParameter(4, $team->getHomepage())
            ->setParameter(5, $team->getLogotype())
            ->setParameter(6, StringFormatterUtils::getRandomString(20))
            ->setParameter(7, $team->getTeamId());

        $queryBuilder->execute();

    }

}
