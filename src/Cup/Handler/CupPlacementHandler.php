<?php

namespace myrisk\Cup\Handler;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\Cup;
use myrisk\Cup\CupPlacement;
use myrisk\Cup\Team;

class CupPlacementHandler {

    private const DB_TABLE_NAME_CUPS_PLACEMENTS = "cups_placements";

    public static function getPlacementByCupAndTeam(Cup $cup, Team $team): CupPlacement
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PLACEMENTS)
            ->where('cupID = ?', 'teamID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $team->getTeamId());

        $placement_query = $queryBuilder->execute();
        $placement_result = $placement_query->fetch();

        if (empty($placement_result)) {
            throw new \UnexpectedValueException('unknown_cup_placement');
        }

        $placement = new CupPlacement();
        $placement->setPlacementId((int) $placement_result['pID']);
        $placement->setRanking($placement_result['ranking']);

        return $placement;

    }

    public static function savePlacement(Cup $cup, Team $team, CupPlacement $placement): void
    {

        if (is_null($placement->getPlacementId())) {
            self::insertPlacement($cup, $team, $placement);
        } else {
            self::updatePlacement($cup, $team, $placement);
        }

    }

    private static function insertPlacement(Cup $cup, Team $team, CupPlacement $placement): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PLACEMENTS)
            ->values(
                    [
                        'cupID' => '?',
                        'teamID' => '?',
                        'ranking' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getCupId(),
                        1 => $team->getTeamId(),
                        2 => $placement->getRanking()
                    ]
                );

        $queryBuilder->execute();

    }

    private static function updatePlacement(Cup $cup, Team $team, CupPlacement $placement): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PLACEMENTS)
            ->set("cupID", "?")
            ->set("teamID", "?")
            ->set("ranking", "?")
            ->where('pID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $team->getTeamId())
            ->setParameter(2, $placement->getRanking())
            ->setParameter(3, $placement->getPlacementId());

        $queryBuilder->execute();

    }

}
