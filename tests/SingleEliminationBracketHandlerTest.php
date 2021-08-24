<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Game;
use webspell_ng\User;
use webspell_ng\Enums\UserEnums;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\GameHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\CupMatch;
use myrisk\Cup\Rule;
use myrisk\Cup\SingleEliminationBracket;
use myrisk\Cup\UserParticipant;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Handler\CupHandler;
use myrisk\Cup\Handler\ParticipantHandler;
use myrisk\Cup\Handler\RuleHandler;
use myrisk\Cup\Handler\SingleEliminationBracketHandler;

final class SingleEliminationBracketHandlerTest extends TestCase
{

    /**
     * @var Game $rule
     */
    private static $game;

    /**
     * @var Rule $rule
     */
    private static $rule;

    /**
     * @var User $user_01
     */
    private static $user_01;

    /**
     * @var User $user_02
     */
    private static $user_02;

    /**
     * @var User $user_03
     */
    private static $user_03;

    /**
     * @var User $user_04
     */
    private static $user_04;

    /**
     * @var User $user_05
     */
    private static $user_05;

    public static function setUpBeforeClass(): void
    {

        self::$game = GameHandler::getGameByGameId(1);

        $new_rule = new Rule();
        $new_rule->setGame(self::$game);
        $new_rule->setName("Test Rule " . StringFormatterUtils::getRandomString(10));
        $new_rule->setText(StringFormatterUtils::getRandomString(10));
        $new_rule->setLastChangeOn(
            new \DateTime("now")
        );

        self::$rule = RuleHandler::saveRule($new_rule);

        $new_user_01 = new User();
        $new_user_01->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user_01->setFirstname(StringFormatterUtils::getRandomString(10));
        $new_user_01->setEmail(StringFormatterUtils::getRandomString(10) . "@myrisk-ev.de");
        $new_user_01->setSex(UserEnums::SEXUALITY_WOMAN);
        $new_user_01->setTown(StringFormatterUtils::getRandomString(10));
        $new_user_01->setBirthday(new \DateTime("2020-09-04 00:00:00"));
        $new_user_01->setCountry(
            CountryHandler::getCountryByCountryShortcut("uk")
        );

        self::$user_01 = UserHandler::saveUser($new_user_01);

        $new_user_02 = new User();
        $new_user_02->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user_02->setFirstname(StringFormatterUtils::getRandomString(10));
        $new_user_02->setEmail(StringFormatterUtils::getRandomString(10) . "@myrisk-ev.de");
        $new_user_02->setSex(UserEnums::SEXUALITY_WOMAN);
        $new_user_02->setTown(StringFormatterUtils::getRandomString(10));
        $new_user_02->setBirthday(new \DateTime("2020-09-04 00:00:00"));
        $new_user_02->setCountry(
            CountryHandler::getCountryByCountryShortcut("uk")
        );

        self::$user_02 = UserHandler::saveUser($new_user_02);

        $new_user_03 = new User();
        $new_user_03->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user_03->setFirstname(StringFormatterUtils::getRandomString(10));
        $new_user_03->setEmail(StringFormatterUtils::getRandomString(10) . "@myrisk-ev.de");
        $new_user_03->setSex(UserEnums::SEXUALITY_WOMAN);
        $new_user_03->setTown(StringFormatterUtils::getRandomString(10));
        $new_user_03->setBirthday(new \DateTime("2020-09-04 00:00:00"));
        $new_user_03->setCountry(
            CountryHandler::getCountryByCountryShortcut("uk")
        );

        self::$user_03 = UserHandler::saveUser($new_user_03);

        $new_user_04 = new User();
        $new_user_04->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user_04->setFirstname(StringFormatterUtils::getRandomString(10));
        $new_user_04->setEmail(StringFormatterUtils::getRandomString(10) . "@myrisk-ev.de");
        $new_user_04->setSex(UserEnums::SEXUALITY_WOMAN);
        $new_user_04->setTown(StringFormatterUtils::getRandomString(10));
        $new_user_04->setBirthday(new \DateTime("2020-09-04 00:00:00"));
        $new_user_04->setCountry(
            CountryHandler::getCountryByCountryShortcut("uk")
        );

        self::$user_04 = UserHandler::saveUser($new_user_04);

        $new_user_05 = new User();
        $new_user_05->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user_05->setFirstname(StringFormatterUtils::getRandomString(10));
        $new_user_05->setEmail(StringFormatterUtils::getRandomString(10) . "@myrisk-ev.de");
        $new_user_05->setSex(UserEnums::SEXUALITY_WOMAN);
        $new_user_05->setTown(StringFormatterUtils::getRandomString(10));
        $new_user_05->setBirthday(new \DateTime("2020-09-04 00:00:00"));
        $new_user_05->setCountry(
            CountryHandler::getCountryByCountryShortcut("uk")
        );

        self::$user_05 = UserHandler::saveUser($new_user_05);

    }

    public function testIfSingleEliminationBracketCanBeCreated(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');

        $new_cup = new Cup();
        $new_cup->setName("Test Cup " . StringFormatterUtils::getRandomString(10));
        $new_cup->setMode(CupEnums::CUP_MODE_1ON1);
        $new_cup->setSize(CupEnums::CUP_SIZE_8);
        $new_cup->setStatus(CupEnums::CUP_STATUS_REGISTRATION);
        $new_cup->setCheckInDateTime($datetime_now);
        $new_cup->setStartDateTime($datetime_later);
        $new_cup->setGame(self::$game);
        $new_cup->setRule(self::$rule);
        $new_cup->setIsSaved(true);
        $new_cup->setIsAdminCup(true);

        $saved_cup = CupHandler::saveCup($new_cup);

        $user_participant_01 = new UserParticipant();
        $user_participant_01->setUser(self::$user_01);

        $user_participant_02 = new UserParticipant();
        $user_participant_02->setUser(self::$user_02);

        $user_participant_03 = new UserParticipant();
        $user_participant_03->setUser(self::$user_03);

        $user_participant_04 = new UserParticipant();
        $user_participant_04->setUser(self::$user_04);

        $user_participant_05 = new UserParticipant();
        $user_participant_05->setUser(self::$user_05);

        ParticipantHandler::joinCup($saved_cup, $user_participant_01);
        ParticipantHandler::confirmCupParticipation($saved_cup, $user_participant_01);

        ParticipantHandler::joinCup($saved_cup, $user_participant_02);
        ParticipantHandler::confirmCupParticipation($saved_cup, $user_participant_02);

        ParticipantHandler::joinCup($saved_cup, $user_participant_03);
        ParticipantHandler::confirmCupParticipation($saved_cup, $user_participant_03);

        ParticipantHandler::joinCup($saved_cup, $user_participant_04);
        ParticipantHandler::confirmCupParticipation($saved_cup, $user_participant_04);

        ParticipantHandler::joinCup($saved_cup, $user_participant_05);
        ParticipantHandler::confirmCupParticipation($saved_cup, $user_participant_05);

        $cup = CupHandler::getCupByCupId($saved_cup->getCupId());

        $single_elimination_bracket = SingleEliminationBracketHandler::saveBracket(
            $cup
        );

        $this->assertInstanceOf(SingleEliminationBracket::class, $single_elimination_bracket, "Instance is expected.");
        $this->assertNotEmpty($single_elimination_bracket->getBracketRounds(), "Bracket rounds are set in general!");

        $bracket_rounds = $single_elimination_bracket->getBracketRounds();

        $first_bracket_round = $bracket_rounds[0];

        $this->assertEquals(1, $first_bracket_round->getRoundIdentifier(), "Round identifier is expected.");
        $this->assertEquals(4, count($first_bracket_round->getMatches()), "Count of matches is expected.");

        $second_bracket_round = $bracket_rounds[1];

        $this->assertEquals(2, $second_bracket_round->getRoundIdentifier(), "Round identifier is expected.");
        $this->assertEquals(2, count($second_bracket_round->getMatches()), "Count of matches is expected.");

        $third_bracket_round = $bracket_rounds[2];

        $this->assertEquals(3, $third_bracket_round->getRoundIdentifier(), "Round identifier is expected.");
        $this->assertEquals(1, count($third_bracket_round->getMatches()), "Count of matches is expected.");

        $final_match = $third_bracket_round->getMatches()[0];

        $this->assertInstanceOf(CupMatch::class, $final_match, "CupMatch instance is returned.");
        $this->assertEquals(3, $final_match->getRoundIdentifier(), "Match round is expected.");
        $this->assertTrue($final_match->isWinnerBracket(), "Match is the winner bracket final.");

    }

}
