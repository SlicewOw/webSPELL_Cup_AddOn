<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use myrisk\Cup\TeamMemberPosition;

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
        $member->setPosition(
            TeamMemberPositionHandler::getPositionByPositionId($member_result['position'])
        );
        $member->setJoinDate(
            DateUtils::getDateTimeByMktimeValue($member_result['join_date'])
        );
        $member->setIsActive($member_result['active']);

        $left_date_value = $member_result['left_date'];
        if ($left_date_value > 0) {
            $member->setLeftDate(
                DateUtils::getDateTimeByMktimeValue($member_result['left_date'])
            );
        }

        $kick_id = $member_result['kickID'];
        if ($kick_id > 0) {
            $member->setKickId($kick_id);
        }

        return $member;

    }

}
