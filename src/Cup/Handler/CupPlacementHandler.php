<?php

namespace myrisk\Cup\Handler;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\Cup;
use myrisk\Cup\CupPlacement;
use myrisk\Cup\Team;

class CupPlacementHandler {

    private const DB_TABLE_NAME_CUPS_PLACEMENTS = "cups_placements";

    /**
     * @return array<CupPlacement>
     */
    public static function getPlacementsOfCup(Cup $cup): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PLACEMENTS)
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId())
            ->orderBy("ranking", "ASC");

        $placement_query = $queryBuilder->executeQuery();
        $placement_results = $placement_query->fetchAllAssociative();

        if (empty($placement_result)) {
            throw new \UnexpectedValueException('unknown_cup_placement');
        }

        $placements = array();

        foreach ($placement_results as $placement_result) {

            array_push(
                $placements,
                self::getPlacementByQueryResult($cup, $placement_result)
            );

        }

        return $placements;

    }

    public static function getPlacementByCupAndTeam(Cup $cup, Team $team): CupPlacement
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PLACEMENTS)
            ->where('cupID = ?', 'teamID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $team->getTeamId());

        $placement_query = $queryBuilder->executeQuery();
        $placement_result = $placement_query->fetchAssociative();

        if (empty($placement_result)) {
            throw new \UnexpectedValueException("unknown_cup_placement");
        }

        return self::getPlacementByQueryResult($cup, $placement_result);

    }

    /**
     * @param array<mixed> $query_result
     */
    private static function getPlacementByQueryResult(Cup $cup, array $query_result): CupPlacement
    {

        $placement = new CupPlacement();
        $placement->setPlacementId((int) $query_result['pID']);
        $placement->setRanking($query_result['ranking']);

        $team_id = (int) $query_result['teamID'];

        if ($cup->isTeamCup()) {
            $placement->setReceiver(
                UserHandler::getUserByUserId($team_id)
            );
        } else {
            $placement->setReceiver(
                TeamHandler::getTeamByTeamId($team_id)
            );
        }

        return $placement;

    }

    public static function savePlacement(Cup $cup, CupPlacement $placement): CupPlacement
    {

        if (is_null($placement->getPlacementId())) {
            $placement = self::insertPlacement($cup, $placement);
        } else {
            self::updatePlacement($cup, $placement);
        }

        return $placement;

    }

    private static function insertPlacement(Cup $cup, CupPlacement $placement): CupPlacement
    {

        $receiver_id = $cup->isTeamCup() ? $placement->getReceiver()->getTeamId() : $placement->getReceiver()->getUserId();

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
                        1 => $receiver_id,
                        2 => $placement->getRanking()
                    ]
                );

        $queryBuilder->executeQuery();

        $placement->setPlacementId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $placement;

    }

    private static function updatePlacement(Cup $cup, CupPlacement $placement): void
    {

        $receiver_id = $cup->isTeamCup() ? $placement->getReceiver()->getTeamId() : $placement->getReceiver()->getUserId();

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_PLACEMENTS)
            ->set("cupID", "?")
            ->set("teamID", "?")
            ->set("ranking", "?")
            ->where('pID = ?')
            ->setParameter(0, $cup->getCupId())
            ->setParameter(1, $receiver_id)
            ->setParameter(2, $placement->getRanking())
            ->setParameter(3, $placement->getPlacementId());

        $queryBuilder->executeQuery();

    }

}
