<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\GameHandler;

use myrisk\Cup\MapPool;

class MapPoolHandler {

    private const DB_TABLE_NAME_CUPS_MAP_POOL = "cups_mappool";

    public static function getMapPoolById(int $cup_id): MapPool
    {

        if (!Validator::numericVal()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException('cup_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_MAP_POOL)
            ->where('mappoolID = ?')
            ->setParameter(0, $cup_id);

        $pool_query = $queryBuilder->execute();
        $pool_result = $pool_query->fetch(FetchMode::MIXED);

        if (empty($pool_result)) {
            throw new \InvalidArgumentException('unknown_cup');
        }

        $map_pool = new MapPool();
        $map_pool->setMapPoolId((int) $pool_result['mappoolID']);
        $map_pool->setName($pool_result['name']);
        $map_pool->setMaps(
            unserialize($pool_result['maps'])
        );
        $map_pool->setGame(
            GameHandler::getGameByGameId((int) $pool_result['gameID'])
        );
        $map_pool->setIsDeleted(
            ((int) $pool_result['deleted'] == 1)
        );

        return $map_pool;

    }

    /**
     * @return array<MapPool>
     */
    public static function getAllMapPools(): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_MAP_POOL)
            ->where('deleted = 0')
            ->orderBy("gameID", "ASC")
            ->addOrderBy("name", "ASC");

        $pool_query = $queryBuilder->execute();

        $map_pools = array();
        while ($pool_result = $pool_query->fetch(FetchMode::MIXED))
        {
            array_push(
                $map_pools,
                self::getMapPoolById((int) $pool_result['mappoolID'])
            );
        }

        return $map_pools;

    }

    public static function saveMapPool(MapPool $map_pool): MapPool
    {

        if (is_null($map_pool->getMapPoolId())) {
            $map_pool = self::insertMapPool($map_pool);
        } else {
            self::updateMapPool($map_pool);
        }

        return self::getMapPoolById($map_pool->getMapPoolId());

    }

    private static function insertMapPool(MapPool $map_pool): MapPool
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_MAP_POOL)
            ->values(
                    [
                        'name' => '?',
                        'gameID' => '?',
                        'maps' => '?',
                        'deleted' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $map_pool->getName(),
                        1 => $map_pool->getGame()->getGameId(),
                        2 => serialize($map_pool->getMaps()),
                        3 => $map_pool->isDeleted() ? 1 : 0
                    ]
                );

        $queryBuilder->execute();

        $map_pool->setMapPoolId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $map_pool;

    }

    private static function updateMapPool(MapPool $map_pool): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_MAP_POOL)
            ->set("name", "?")
            ->set("gameID", "?")
            ->set("maps", "?")
            ->set("deleted", "?")
            ->where('mappoolID = ?')
            ->setParameter(0, $map_pool->getName())
            ->setParameter(1, $map_pool->getGame()->getGameId())
            ->setParameter(2, serialize($map_pool->getMaps()))
            ->setParameter(3, $map_pool->isDeleted() ? 1 : 0)
            ->setParameter(4, $map_pool->getMapPoolId());

        $queryBuilder->execute();

    }

}
