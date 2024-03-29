<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Handler\GameHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Rule;
use myrisk\Cup\Handler\RuleHandler;

final class RuleHandlerTest extends TestCase
{

    public function testIfRuleHandlerReturnsRuleInstance(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_now->setTime((int) $datetime_now->format("H"), (int) $datetime_now->format("i"), (int) $datetime_now->format("s"), 0);

        $game = GameHandler::getGameByGameId(1);

        $new_rule = new Rule();
        $new_rule->setName("Test Rule " . StringFormatterUtils::getRandomString(10));
        $new_rule->setText(StringFormatterUtils::getRandomString(10));
        $new_rule->setGame($game);
        $new_rule->setLastChangeOn($datetime_now);

        $saved_rule = RuleHandler::saveRule($new_rule);

        $this->assertInstanceOf(Rule::class, $saved_rule);

        $rule = RuleHandler::getRuleByRuleId($saved_rule->getRuleId());

        $this->assertInstanceOf(Rule::class, $rule);
        $this->assertEquals($saved_rule->getRuleId(), $rule->getRuleId(), "Rule ID is the same.");
        $this->assertEquals($saved_rule->getName(), $rule->getName(), "Rule name is the same.");
        $this->assertEquals($saved_rule->getText(), $rule->getText(), "Rule test is the same.");
        $this->assertEquals($saved_rule->getLastChangeOn(), $rule->getLastChangeOn(), "Last change on is the same.");
        $this->assertEquals($saved_rule->getGame()->getTag(), $rule->getGame()->getTag(), "Rule game tag is the same.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfRuleIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $rule = RuleHandler::getRuleByRuleId(-1);

        // This line is hopefully never be reached
        $this->assertLessThan(1, $rule->getRuleId());

    }

    public function testIfInvalidArgumentExceptionIsThrownIfGameOfRuleIdIsInvalid(): void
    {

        $this->expectException(TypeError::class);

        RuleHandler::saveRule(new Rule());

    }

    public function testIfInvalidArgumentExceptionIsThrownIfRuleDoesNotExist(): void
    {

        $this->expectException(InvalidArgumentException::class);

        RuleHandler::getRuleByRuleId(99999999);

    }

}
