<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Handler\GameHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\Rule;
use myrisk\Cup\MapPool;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Handler\CupHandler;
use myrisk\Cup\Handler\MapPoolHandler;
use myrisk\Cup\Handler\RuleHandler;

final class CupHandlerTest extends TestCase
{

    /**
     * @var MapPool $map_pool
     */
    private static $map_pool;

    public static function setUpBeforeClass(): void
    {

        $all_map_pools = MapPoolHandler::getAllMapPools();

        if (empty($all_map_pools)) {

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

            self::$map_pool = MapPoolHandler::saveMapPool($new_map_pool);

        } else {
            self::$map_pool = $all_map_pools[0];
        }

    }

    public function testIfCupCanBeSavedAndUpdated(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');
        $description = "Test Description !!111";

        $game = GameHandler::getGameByGameId(1);

        $rule = new Rule();
        $rule->setGame($game);
        $rule->setName("Test Rule " . StringFormatterUtils::getRandomString(10));
        $rule->setText(StringFormatterUtils::getRandomString(10));
        $rule->setLastChangeOn($datetime_now);

        $rule = RuleHandler::saveRule($rule);

        $new_cup = new Cup();
        $new_cup->setName("Test Cup Name");
        $new_cup->setMode(CupEnums::CUP_MODE_5ON5);
        $new_cup->setSize(CupEnums::CUP_SIZE_8);
        $new_cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $new_cup->setCheckInDateTime($datetime_now);
        $new_cup->setStartDateTime($datetime_later);
        $new_cup->setGame($game);
        $new_cup->setRule($rule);
        $new_cup->setIsSaved(false);
        $new_cup->setIsAdminCup(true);
        $new_cup->setDescription($description);

        $cup = CupHandler::saveCup($new_cup);

        $this->assertInstanceOf(Cup::class, $cup);
        $this->assertEquals(CupEnums::CUP_SIZE_8, $cup->getSize(), "Cup size is set correctly.");
        $this->assertEquals(CupEnums::CUP_PHASE_RUNNING, $cup->getPhase(), "Cup phase is set correctly.");
        $this->assertGreaterThan(0, $cup->getRule()->getRuleId(), "Rule is set.");
        $this->assertFalse($cup->isMapVoteDone(), "Map vote is disabled.");
        $this->assertFalse($cup->isSaved(), "Cup is not saved yet.");
        $this->assertTrue($cup->isAdminCup(), "Cup is for admins only.");
        $this->assertNull($cup->getMapPool(), "Map Pool is not set yet.");
        $this->assertFalse($cup->isUsingServers(), "Cup is not using servers.");
        $this->assertEquals($description, $cup->getDescription(), "Cup description is set correctly.");

        $maximum_pps = random_int(1, 89999);

        $changed_cup = $cup;
        $changed_cup->setStatus(CupEnums::CUP_STATUS_FINISHED);
        $changed_cup->setIsSaved(true);
        $changed_cup->setMapVoteEnabled(true);
        $changed_cup->setMaximumOfPenaltyPoints($maximum_pps);
        $changed_cup->setMapPool(self::$map_pool);
        $changed_cup->setIsUsingServers(true);

        $updated_cup = CupHandler::saveCup($changed_cup);

        $this->assertInstanceOf(Cup::class, $updated_cup);
        $this->assertEquals(CupEnums::CUP_SIZE_8, $updated_cup->getSize(), "Cup size is set correctly.");
        $this->assertEquals(CupEnums::CUP_STATUS_FINISHED, $updated_cup->getStatus(), "Cup status is set correctly.");
        $this->assertEquals($maximum_pps, $updated_cup->getMaximumOfPenaltyPoints(), "Maximum of PPS is set correctly.");
        $this->assertTrue($cup->isMapVoteDone(), "Map vote is enabled.");
        $this->assertFalse($cup->isRunning(), "Cup status is not 'running'.");
        $this->assertTrue($updated_cup->isFinished(), "Cup status is set correctly.");
        $this->assertEquals(CupEnums::CUP_PHASE_FINISHED, $updated_cup->getPhase(), "Cup phase is set correctly.");
        $this->assertEquals($cup->getRule()->getRuleId(), $updated_cup->getRule()->getRuleId(), "Rule is set.");
        $this->assertTrue($cup->isSaved(), "Cup is saved.");
        $this->assertTrue($cup->isAdminCup(), "Cup is for admins only.");
        $this->assertEquals(self::$map_pool->getMapPoolId(), $updated_cup->getMapPool()->getMapPoolId(), "Map Pool is set.");
        $this->assertTrue($cup->isUsingServers(), "Cup is using servers.");
        $this->assertEquals($description, $cup->getDescription(), "Cup description is set correctly.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupHandler::getCupByCupId(-1);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupDoesNotExist(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupHandler::getCupByCupId(99999999);

    }

    public function testIfTypeErrorIsThrownIfGameOfCupDoesNotExist(): void
    {

        $this->expectException(TypeError::class);

        $cup = new Cup();
        $cup->setName("Test Cup Name");
        $cup->setMode(CupEnums::CUP_MODE_5ON5);
        $cup->setSize(CupEnums::CUP_SIZE_4);
        $cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $cup->setCheckInDateTime(new \DateTime("now"));
        $cup->setStartDateTime(new \DateTime("now"));
        $cup->setRule(new Rule());

        CupHandler::saveCup($cup);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfRuleOfCupDoesNotExist(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $cup = new Cup();
        $cup->setGame(
            GameHandler::getGameByGameId(2)
        );

        CupHandler::saveCup($cup);

    }

}
