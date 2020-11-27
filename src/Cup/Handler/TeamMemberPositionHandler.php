<?php

namespace myrisk\Cup\Handler;

use myrisk\Cup\Enum\TeamEnums;
use myrisk\Cup\TeamMember;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\TeamMemberPosition;

class TeamMemberPositionHandler {

    private const DB_TABLE_NAME_POSITION = "cups_teams_position";

    public static function getPositionByPositionId(int $position_id): TeamMemberPosition
    {

        if (!Validator::numericVal()->min(1)->validate($position_id)) {
            throw new \InvalidArgumentException('position_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_POSITION)
            ->where('positionID = ?')
            ->setParameter(0, $position_id);

        $position_query = $queryBuilder->execute();
        $position_result = $position_query->fetch();

        if (empty($position_result)) {
            throw new \InvalidArgumentException('unknown_cup_team_member_position');
        }

        $position = new TeamMemberPosition();
        $position->setPositionId($position_result['positionID']);
        $position->setPosition($position_result['name']);
        $position->setSort($position_result['sort']);

        return $position;

    }

    public static function getAdminPosition(): TeamMemberPosition
    {
        return self::getPositionByName(TeamEnums::TEAM_MEMBER_POSITION_ADMIN);
    }

    public static function getCoachPosition(): TeamMemberPosition
    {
        return self::getPositionByName(TeamEnums::TEAM_MEMBER_POSITION_COACH);
    }

    private static function getPositionByName(string $position_name): TeamMemberPosition
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_POSITION)
            ->where('name = ?')
            ->setParameter(0, $position_name);

        $position_query = $queryBuilder->execute();
        $position_result = $position_query->fetch();

        if (empty($position_result)) {
            // @codeCoverageIgnoreStart
            throw new \InvalidArgumentException('unknown_cup_team_member_position');
            // @codeCoverageIgnoreEnd
        }

        $position = new TeamMemberPosition();
        $position->setPositionId($position_result['positionID']);
        $position->setPosition($position_result['name']);
        $position->setSort($position_result['sort']);

        return $position;

    }

    public static function saveTeamMemberPosition(TeamMemberPosition $position): TeamMemberPosition
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_POSITION)
            ->values(
                    [
                        'name' => '?',
                        'sort' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $position->getPosition(),
                        1 => $position->getSort()
                    ]
                );

        $queryBuilder->execute();

        $position->setPositionId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $position;

    }

}