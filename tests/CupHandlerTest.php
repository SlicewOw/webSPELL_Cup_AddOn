<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \webspell_ng\Game;
use \webspell_ng\Handler\GameHandler;

use \myrisk\Cup\Cup;
use \myrisk\Cup\Rule;
use \myrisk\Cup\Enum\CupEnums;
use \myrisk\Cup\Handler\CupHandler;
use \myrisk\Cup\Handler\RuleHandler;

final class CupHandlerTest extends TestCase
{

    private function getRandomString(): string
    {
        return bin2hex(random_bytes(10));
    }

    public function testIfCupCanBeSavedAndUpdated(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');

        $game = GameHandler::getGameByGameId(1);

        $rule = new Rule();
        $rule->setGame($game);
        $rule->setName("Test Rule " . $this->getRandomString());
        $rule->setText($this->getRandomString());
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

        $saved_cup = CupHandler::saveCup($new_cup);

        $cup = CupHandler::getCupByCupId($saved_cup->getCupId());

        $this->assertInstanceOf(Cup::class, $cup);
        $this->assertEquals(CupEnums::CUP_SIZE_8, $cup->getSize(), "Cup size is set correctly.");
        $this->assertEquals(CupEnums::CUP_PHASE_RUNNING, $cup->getPhase(), "Cup phase is set correctly.");
        $this->assertGreaterThan(0, $cup->getRule()->getRuleId(), "Rule is set.");

        $changed_cup = $cup;
        $changed_cup->setStatus(CupEnums::CUP_STATUS_FINISHED);

        $updated_cup = CupHandler::saveCup($changed_cup);

        $this->assertInstanceOf(Cup::class, $updated_cup);
        $this->assertEquals(CupEnums::CUP_SIZE_8, $updated_cup->getSize(), "Cup size is set correctly.");
        $this->assertEquals(CupEnums::CUP_STATUS_FINISHED, $updated_cup->getStatus(), "Cup status is set correctly.");
        $this->assertEquals(CupEnums::CUP_PHASE_FINISHED, $updated_cup->getPhase(), "Cup phase is set correctly.");
        $this->assertEquals($cup->getRule()->getRuleId(), $updated_cup->getRule()->getRuleId(), "Rule is set.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $cup = CupHandler::getCupByCupId(-1);

        // This line is hopefully never be reached
        $this->assertLessThan(1, $cup->getCupId());

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
