<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use myrisk\Cup\Handler\TeamMemberHandler;

class TeamHandler {

    public static function getTeamByTeamId(int $team_id): Team
    {

        if (!Validator::numericVal()->min(1)->validate($team_id)) {
            throw new \InvalidArgumentException('team_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_teams')
            ->where('teamID = ?')
            ->setParameter(0, $team_id);

        $team_query = $queryBuilder->execute();
        $team_result = $team_query->fetch();

        if (!$team_result || count($team_result) < 1) {
            throw new \InvalidArgumentException('unknown_cup_team');
        }

        $admin_id = $team_result['userID'];

        $team = new Team();
        $team->setTeamId($team_result['teamID']);
        $team->setCreationDate($team_result['date']);
        $team->setName($team_result['name']);
        $team->setTag($team_result['tag']);
        $team->setCountry($team_result['country']);
        $team->setHomepage($team_result['hp']);
        $team->setLogotype($team_result['logotype']);
        $team->setIsDeleted($team_result['deleted']);
        $team->addMember(
            TeamMemberHandler::getMemberByUserIdAndTeam($admin_id, $team)
        );

        return $team;

    }

}
