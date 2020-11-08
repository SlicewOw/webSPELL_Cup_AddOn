<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\TeamMemberPosition;

class TeamMemberPositionHandler {

    public static function getPositionByPositionId(int $position_id): TeamMemberPosition
    {

        if (!Validator::numericVal()->min(1)->validate($position_id)) {
            throw new \InvalidArgumentException('position_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_teams_position')
            ->where('positionID = ?')
            ->setParameter(0, $position_id);

        $position_query = $queryBuilder->execute();
        $position_result = $position_query->fetch();

        if (!$position_result || count($position_result) < 1) {
            throw new \InvalidArgumentException('unknown_cup_team_member_position');
        }

        $position = new TeamMemberPosition();
        $position->setPositionId($position_result['positionID']);
        $position->setPosition($position_result['name']);
        $position->setSort($position_result['sort']);

        return $position;

    }

}