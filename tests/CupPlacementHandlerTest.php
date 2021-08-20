<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Handler\GameHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\CupPlacement;
use myrisk\Cup\Rule;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Handler\CupHandler;
use myrisk\Cup\Handler\CupPlacementHandler;
use myrisk\Cup\Handler\RuleHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\User;

final class CupPlacementHandlerTest extends TestCase
{

    /**
     * @var Cup $cup
     */
    private static $cup;

    public static function setUpBeforeClass(): void
    {

        $game = GameHandler::getGameByGameId(1);

        $new_rule = new Rule();
        $new_rule->setGame($game);
        $new_rule->setName("Test Rule " . StringFormatterUtils::getRandomString(10));
        $new_rule->setText(StringFormatterUtils::getRandomString(10));
        $new_rule->setLastChangeOn(
            new \DateTime("30 minutes ago")
        );

        $rule = RuleHandler::saveRule($new_rule);

        $new_cup = new Cup();
        $new_cup->setName("Test Cup " . StringFormatterUtils::getRandomString(10));
        $new_cup->setMode(CupEnums::CUP_MODE_1ON1);
        $new_cup->setRule($rule);
        $new_cup->setGame($game);
        $new_cup->setSize(CupEnums::CUP_SIZE_8);
        $new_cup->setCheckInDateTime(
            new \DateTime("1 minute ago")
        );
        $new_cup->setStartDateTime(
            new \DateTime("+30 minutes")
        );

        self::$cup = CupHandler::saveCup($new_cup);

    }

    public function testIfCupPlacementCanBeSavedAndUpdated(): void
    {

        $user_id = 1;
        $ranking = random_int(1, 4);

        $new_placement = new CupPlacement();
        $new_placement->setReceiver(
            UserHandler::getUserByUserId($user_id)
        );
        $new_placement->setRanking((string) $ranking);

        $saved_placement = CupPlacementHandler::savePlacement(self::$cup, $new_placement);

        $this->assertInstanceOf(CupPlacement::class, $saved_placement, "Placement is saved.");
        $this->assertGreaterThan(0, $saved_placement->getPlacementId(), "Placement ID is set.");
        $this->assertEquals((string) $ranking, $saved_placement->getRanking(), "Ranking is set.");
        $this->assertInstanceOf(User::class, $saved_placement->getReceiver(), "Receiver is a 'User'.");
        $this->assertEquals($user_id, $saved_placement->getReceiver()->getUserId(), "User ID is set.");

        $updated_ranking = "5-8";

        $saved_placement->setRanking($updated_ranking);

        $changed_placement = CupPlacementHandler::savePlacement(self::$cup, $saved_placement);

        $this->assertInstanceOf(CupPlacement::class, $changed_placement, "Placement is saved.");
        $this->assertEquals($saved_placement->getPlacementId(), $changed_placement->getPlacementId(), "Placement ID is set.");
        $this->assertEquals($updated_ranking, $changed_placement->getRanking(), "Ranking is set.");
        $this->assertInstanceOf(User::class, $changed_placement->getReceiver(), "Receiver is a 'User'.");
        $this->assertEquals($user_id, $changed_placement->getReceiver()->getUserId(), "User ID is set.");

    }

}
