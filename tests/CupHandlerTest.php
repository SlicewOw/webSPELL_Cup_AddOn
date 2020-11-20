<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \webspell_ng\Handler\GameHandler;

use \myrisk\Cup\Cup;
use \myrisk\Cup\Rule;
use \myrisk\Cup\Enum\CupEnums;
use \myrisk\Cup\Handler\CupHandler;

final class CupHandlerTest extends TestCase
{

    public function testIfCupHandlerReturnsCupInstance(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');

        $game = GameHandler::getGameByGameId(1);

        $rule = new Rule();
        $rule->setRuleId(123);
        $rule->setGame($game);
        $rule->setName("Test Rule 123");
        $rule->setText("empty rule!");
        $rule->setLastChangeOn($datetime_now);

        $new_cup = new Cup();
        $new_cup->setName("Test Cup Name");
        $new_cup->setMode(CupEnums::CUP_MODE_5ON5);
        $new_cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $new_cup->setCheckInDateTime($datetime_now);
        $new_cup->setStartDateTime($datetime_later);
        $new_cup->setGame($game);
        $new_cup->setRule($rule);

        $saved_cup = CupHandler::saveCup($new_cup);

        $cup = CupHandler::getCupByCupId($saved_cup->getCupId());

        $this->assertInstanceOf(Cup::class, $cup);

    }

}
