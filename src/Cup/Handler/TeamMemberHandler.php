<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use webspell_ng\Enums\UserEnums;
use webspell_ng\Utils\ValidationUtils;

class TeamMemberHandler {

    private const DB_TABLE_NAME_TEAM_MEMBERS = "cups_teams_member";

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
            ->where('teamID = ? AND active = ?')
            ->setParameter(0, $team->getTeamId())
            ->setParameter(1, 1);

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

            $left_date_value = $member_result['left_date'];
            if ($left_date_value > 0) {

                $member->setLeftDate(
                    DateUtils::getDateTimeByMktimeValue($member_result['left_date'])
                );

                $kick_id = $member_result['kickID'];
                if ($kick_id > 0) {
                    $member->setKickId($kick_id);
                }

            }

            array_push($members, $member);

        }

        return $members;

    }

    public static function getMemberByUserIdAndTeam(int $user_id, Team $team): TeamMember
    {

        if (!Validator::numericVal()->min(1)->validate($user_id)) {
            throw new \InvalidArgumentException('member_id_value_is_invalid');
        }

        $team_members = self::getMembersOfTeam($team);

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

            if (is_null($member->getUser()->getUserId())) {
                throw new \InvalidArgumentException("user_of_team_member_is_not_set_properly");
            }

            self::insertTeamMember($team, $member);

        }

        return TeamHandler::getTeamByTeamId($team->getTeamId());

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

    }

    /**
     * @param array<TeamMember> $members
     */
    public static function updateTeamMember(Team $team, array $members): void
    {

        foreach ($members as $member) {

            $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
            $queryBuilder
                ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAM_MEMBERS)
                ->set("position", "?")
                ->where(
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->eq('teamID', '?'),
                        $queryBuilder->expr()->eq('userID', '?')
                    )
                )
                ->setParameter(0, $member->getPosition()->getPositionId())
                ->setParameter(1, $team->getTeamId())
                ->setParameter(2, $member->getUser()->getUserId());

            $queryBuilder->execute();

        }


    }

    // TODO: Use class 'UserSession' for kickID
    public static function kickMember(Team $team, TeamMember $member): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_TEAM_MEMBERS)
            ->set("left_date", "?")
            ->set("kickID", "?")
            ->set("active", "?")
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('teamID', '?'),
                    $queryBuilder->expr()->eq('userID', '?')
                )
            )
            ->setParameter(0, time())
            ->setParameter(1, 1)
            ->setParameter(2, 0)
            ->setParameter(3, $team->getTeamId())
            ->setParameter(4, $member->getUser()->getUserId());

        $queryBuilder->execute();

    }

}
