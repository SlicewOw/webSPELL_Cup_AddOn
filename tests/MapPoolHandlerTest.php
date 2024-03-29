<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Handler\GameHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\MapPool;
use myrisk\Cup\Handler\MapPoolHandler;


final class MapPoolHandlerTest extends TestCase
{

    public function testIfMapPoolCanBeSavedAndUpdated(): void
    {

        $map_pool_name = StringFormatterUtils::getRandomString(10);

        $new_map_pool = new MapPool();
        $new_map_pool->setName($map_pool_name);
        $new_map_pool->setGame(
            GameHandler::getGameByGameId(1)
        );
        $new_map_pool->setMaps(
            array(
                "de_test1",
                "de_test2",
                "de_test3"
            )
        );

        $saved_map_pool = MapPoolHandler::saveMapPool($new_map_pool);

        $this->assertGreaterThan(0, $saved_map_pool->getMapPoolId(), "MapPool ID is set.");
        $this->assertEquals($map_pool_name, $saved_map_pool->getName(), "MapPool name is set.");
        $this->assertEquals(1, $saved_map_pool->getGame()->getGameId(), "MapPool game is set.");
        $this->assertEquals(3, count($saved_map_pool->getMaps()), "MapPool maps are set.");
        $this->assertFalse($saved_map_pool->isDeleted(), "MapPool is not deleted.");

        $added_map_array = $saved_map_pool->getMaps();
        $added_map_array[] = "new_map";

        $changed_map_pool = $saved_map_pool;
        $changed_map_pool->setMaps($added_map_array);
        $changed_map_pool->setIsDeleted(true);

        $updated_map_pool = MapPoolHandler::saveMapPool($changed_map_pool);

        $this->assertGreaterThan(0, $updated_map_pool->getMapPoolId(), "MapPool ID is set.");
        $this->assertEquals($map_pool_name, $updated_map_pool->getName(), "MapPool name is set.");
        $this->assertEquals(1, $updated_map_pool->getGame()->getGameId(), "MapPool game is set.");
        $this->assertEquals(4, count($updated_map_pool->getMaps()), "MapPool maps are set.");
        $this->assertTrue($updated_map_pool->isDeleted(), "MapPool is deleted.");

    }

    public function testIfAllActiveMapPoolsAreReturned(): void
    {

        $new_map_pool = new MapPool();
        $new_map_pool->setName("Test Map Pool " . StringFormatterUtils::getRandomString(10));
        $new_map_pool->setGame(
            GameHandler::getGameByGameId(1)
        );
        $new_map_pool->setMaps(
            array(
                "de_test1"
            )
        );

        MapPoolHandler::saveMapPool($new_map_pool);

        $all_active_map_pools = MapPoolHandler::getAllMapPools();

        $this->assertGreaterThan(0, count($all_active_map_pools), "Map Pools are returned.");

        $all_map_pools_are_active = true;

        foreach ($all_active_map_pools as $map_pool)
        {

            if ($map_pool->isDeleted()) {
                $all_map_pools_are_active = false;
            }

        }

        $this->assertTrue($all_map_pools_are_active, "All map pools are active.");

    }

    public function testIfMapPoolsAreReturnedAsOptions(): void
    {

        $map_pools_as_options = MapPoolHandler::getMapPoolsAsOptions();

        $this->assertNotEmpty($map_pools_as_options, "Options are returned in general.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfMapPoolIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        MapPoolHandler::getMapPoolById(-1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfMapPoolDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        MapPoolHandler::getMapPoolById(9999999);

    }

}
