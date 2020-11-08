<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;

class TeamMemberHandler {

    public static function getMemberByUserIdAndTeam(int $user_id, Team $team): TeamMember
    {

        if (!Validator::numericVal()->min(1)->validate($user_id)) {
            throw new \InvalidArgumentException('member_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_teams_member')
            ->where('teamID = ? AND userID = ?')
            ->setParameter(0, $team->getTeamId())
            ->setParameter(1, $user_id);

        $member_query = $queryBuilder->execute();
        $member_result = $member_query->fetch();

        if (!$member_result || count($member_result) < 1) {
            throw new \InvalidArgumentException('unknown_cup_team_member');
        }

        $member = new TeamMember();
        $member->setMemberId($member_result['memberID']);
        $member->setUser(
            UserHandler::getUserByUserId($member_result['userID'])
        );
        $member->setPosition($member_result['position']);
        $member->setJoinDate($member_result['join_date']);
        $member->setLeftDate($member_result['left_date']);
        $member->setKickId($member_result['kickID']);
        $member->setIsActive($member_result['active']);

        return $member;

    }

}
