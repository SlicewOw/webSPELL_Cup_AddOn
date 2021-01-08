<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;

use Respect\Validation\Validator;

use webspell_ng\UserLog;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Handler\UserLogHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;

class TeamMemberHandler {

    public const DB_TABLE_NAME_TEAM_MEMBERS = "cups_teams_member";

    /**
     * @return array<TeamMember>
     */
    public static function getMembersOfTeam(Team $team): array
    {

        $members = array();

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAM_MEMBERS)
            ->where('teamID = ?')
            ->setParameter(0, $team->getTeamId());

        $member_query = $queryBuilder->execute();

        while ($member_result = $member_query->fetch(FetchMode::MIXED)) {

            $member = new TeamMember();
            $member->setMemberId((int) $member_result['memberID']);
            $member->setIsActive($member_result['active']);
            $member->setUser(
                UserHandler::getUserByUserId((int) $member_result['userID'])
            );
            $member->setPosition(
                TeamMemberPositionHandler::getPositionByPositionId($member_result['position'])
            );
            $member->setJoinDate(
                DateUtils::getDateTimeByMktimeValue($member_result['join_date'])
            );

            if (!is_null($member_result['left_date'])) {
                $member->setLeftDate(
                    DateUtils::getDateTimeByMktimeValue($member_result['left_date'])
                );
            }

            if (!is_null($member_result['kickID'])) {
                $member->setKickId($member_result['kickID']);
            }

            array_push($members, $member);

        }

        return $members;

    }

    /**
     * @return array<TeamMember>
     */
    public static function getActiveMembersOfTeam(Team $team): array
    {

        $all_members = self::getMembersOfTeam($team);

        $active_members = array();
        foreach ($all_members as $member) {

            if ($member->isActive()) {
                array_push(
                    $active_members,
                    $member
                );
            }

        }

        return $active_members;

    }

    public static function getMemberByUserIdAndTeam(int $user_id, Team $team): TeamMember
    {

        if (!Validator::numericVal()->min(1)->validate($user_id)) {
            throw new \InvalidArgumentException('member_id_value_is_invalid');
        }

        $team_members = self::getActiveMembersOfTeam($team);

        foreach ($team_members as $member) {

            if ($member->getUser()->getUserId() == $user_id) {
                return $member;
            }

        }

        throw new \UnexpectedValueException("team_member_not_found");

    }

    public static function saveTeamMembers(Team $team): Team
    {

        $tmp_members = $team->getMembers();
        foreach ($tmp_members as $member) {
            self::saveSingleTeamMember($team, $member);
        }

        return TeamHandler::getTeamByTeamId($team->getTeamId());

    }

    public static function saveSingleTeamMember(Team $team, TeamMember $member): void
    {

        if (is_null($member->getUser()->getUserId())) {
            throw new \InvalidArgumentException("user_of_team_member_is_not_set_properly");
        }

        if (is_null($member->getMemberId())) {
            self::insertTeamMember($team, $member);
        } else {
            self::updateTeamMember($team, $member);
        }

    }

    private static function insertTeamMember(Team $team, TeamMember $member): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAM_MEMBERS)
            ->values(
                    [
                        'userID' => '?',
                        'teamID' => '?',
                        'position' => '?',
                        'join_date' => '?',
                        'active' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $member->getUser()->getUserId(),
                        1 => $team->getTeamId(),
                        2 => $member->getPosition()->getPositionId(),
                        3 => $member->getJoinDate()->getTimestamp(),
                        4 => 1
                    ]
                );

        $queryBuilder->execute();

        $member->setMemberId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

    }

    private static function updateTeamMember(Team $team, TeamMember $member): void
    {

        $left_date = (!is_null($member->getLeftDate())) ? $member->getLeftDate()->getTimestamp() : null;
        $kick_id = (!is_null($member->getKickId())) ? $member->getKickId() : null;

        $is_active = ($member->isActive()) ? 1 : 0;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAM_MEMBERS)
            ->set("position", "?")
            ->set("join_date", "?")
            ->set("left_date", "?")
            ->set("kickID", "?")
            ->set("active", "?")
            ->where("teamID = ?", "userID = ?")
            ->setParameter(0, $member->getPosition()->getPositionId())
            ->setParameter(1, $member->getJoinDate()->getTimestamp())
            ->setParameter(2, $left_date)
            ->setParameter(3, $kick_id)
            ->setParameter(4, $is_active)
            ->setParameter(5, $team->getTeamId())
            ->setParameter(6, $member->getUser()->getUserId());

        $queryBuilder->execute();

    }

    public static function joinTeam(Team $team, TeamMember $member): void
    {

        $member->setIsActive(true);
        $member->setLeftDate(null);
        $member->setJoinDate(
            new \DateTime("now")
        );

        self::saveSingleTeamMember($team, $member);

        self::saveUserLogNewCupTeamMember($team, $member);

    }

    public static function leaveTeam(Team $team, TeamMember $member): void
    {

        $member->setIsActive(false);
        $member->setLeftDate(
            new \DateTime("now")
        );

        self::saveSingleTeamMember($team, $member);

        self::saveUserLogLeftCupTeamMember($team, $member);

    }

    public static function kickMember(Team $team, TeamMember $member): void
    {

        // TODO: Use class 'UserSession' for kickID
        $member->setKickId(1);
        $member->setIsActive(false);
        $member->setLeftDate(
            new \DateTime("now")
        );

        self::saveSingleTeamMember($team, $member);

        self::saveUserLogKickedCupTeamMember($team, $member);

    }

    private static function saveUserLogLeftCupTeamMember(Team $team, TeamMember $member): void
    {
        UserLogHandler::saveUserLog(
            $member->getUser(),
            self::getCupTeamMemberUserLog($team, "cup_team_left")
        );
    }

    private static function saveUserLogKickedCupTeamMember(Team $team, TeamMember $member): void
    {
        UserLogHandler::saveUserLog(
            $member->getUser(),
            self::getCupTeamMemberUserLog($team, "cup_team_kicked")
        );
    }

    private static function saveUserLogNewCupTeamMember(Team $team, TeamMember $member): void
    {
        UserLogHandler::saveUserLog(
            $member->getUser(),
            self::getCupTeamMemberUserLog($team, "cup_team_joined")
        );
    }

    private static function getCupTeamMemberUserLog(Team $team, string $info): UserLog
    {
        $log = new UserLog();
        $log->setInfo($info);
        $log->setParentId($team->getTeamId());
        return $log;
    }

}
