<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\Enums\UserEnums;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\GameHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\Rule;
use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Handler\CupHandler;
use myrisk\Cup\Handler\ParticipantHandler;
use myrisk\Cup\Handler\RuleHandler;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Handler\TeamMemberPositionHandler;

final class ParticipantHandlerTest extends TestCase
{

    /**
     * @var Cup $cup_1on1
     */
    private static $cup_1on1;

    /**
     * @var Cup $cup_5on5
     */
    private static $cup_5on5;

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

        $new_1on1_cup = new Cup();
        $new_1on1_cup->setName("Test Cup " . StringFormatterUtils::getRandomString(10));
        $new_1on1_cup->setMode(CupEnums::CUP_MODE_1ON1);
        $new_1on1_cup->setRule($rule);
        $new_1on1_cup->setGame($game);
        $new_1on1_cup->setSize(CupEnums::CUP_SIZE_8);
        $new_1on1_cup->setCheckInDateTime(
            new \DateTime("1 minute ago")
        );
        $new_1on1_cup->setStartDateTime(
            new \DateTime("+30 minutes")
        );

        self::$cup_1on1 = CupHandler::saveCup($new_1on1_cup);

        $new_5on5_cup = new Cup();
        $new_5on5_cup->setName("Test Cup " . StringFormatterUtils::getRandomString(10));
        $new_5on5_cup->setMode(CupEnums::CUP_MODE_5ON5);
        $new_5on5_cup->setRule($rule);
        $new_5on5_cup->setGame($game);
        $new_5on5_cup->setSize(CupEnums::CUP_SIZE_16);
        $new_5on5_cup->setCheckInDateTime(
            new \DateTime("2 minute ago")
        );
        $new_5on5_cup->setStartDateTime(
            new \DateTime("+2 hours")
        );

        self::$cup_5on5 = CupHandler::saveCup($new_5on5_cup);

    }

    public function testIfUserCanJoinAndCheckInAndLeaveACup(): void
    {

        $new_user = new User();
        $new_user->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user->setFirstname(StringFormatterUtils::getRandomString(10));
        $new_user->setLastname(StringFormatterUtils::getRandomString(10));
        $new_user->setEmail(StringFormatterUtils::getRandomString(10) . "@myrisk-ev.de");
        $new_user->setSex(UserEnums::SEXUALITY_WOMAN);
        $new_user->setTown(StringFormatterUtils::getRandomString(10));
        $new_user->setBirthday(new \DateTime("2020-09-04 00:00:00"));
        $new_user->setCountry(
            CountryHandler::getCountryByCountryShortcut("uk")
        );

        $user = UserHandler::saveUser($new_user);

        $this->assertGreaterThan(0, $user->getUserId(), "User is saved correctly.");

        $user_participant = new UserParticipant();
        $user_participant->setUser($user);

        ParticipantHandler::joinCup(self::$cup_1on1, $user_participant);

        $cup = CupHandler::getCupByCupId(self::$cup_1on1->getCupId());

        $participants = $cup->getCupParticipants();

        $this->assertEquals(1, count($participants), "User is a participant.");

        $saved_user_participant_01 = $participants[0];

        $this->assertGreaterThan(0, $saved_user_participant_01->getParticipantId(), "Participant ID is set.");
        $this->assertEquals($user->getUsername(), $saved_user_participant_01->getUser()->getUsername(), "Username is expected.");
        $this->assertFalse($saved_user_participant_01->getCheckedIn(), "User is not checked in yet 01.");
        $this->assertNull($saved_user_participant_01->getCheckInDateTime(), "User is not checked in yet 02.");

        ParticipantHandler::confirmCupParticipation(self::$cup_1on1, $saved_user_participant_01);

        $cup = CupHandler::getCupByCupId(self::$cup_1on1->getCupId());

        $participants = $cup->getCupParticipants();

        $this->assertEquals(1, count($participants), "User is a participant.");

        $saved_user_participant_02 = $participants[0];

        $this->assertEquals($saved_user_participant_01->getParticipantId(), $saved_user_participant_02->getParticipantId(), "Participant ID is set.");
        $this->assertEquals($user->getUsername(), $saved_user_participant_02->getUser()->getUsername(), "Username is expected.");
        $this->assertTrue($saved_user_participant_02->getCheckedIn(), "User is checked in 01.");
        $this->assertNotNull($saved_user_participant_02->getCheckInDateTime(), "User is checked in 02.");

        ParticipantHandler::leaveCup(self::$cup_1on1, $saved_user_participant_02);

        $cup = CupHandler::getCupByCupId(self::$cup_1on1->getCupId());

        $participants = $cup->getCupParticipants();

        $this->assertEquals(0, count($participants), "User is not a participant anymore.");

    }

    public function testIfTeamCanJoinAndCheckInAndLeaveACup(): void
    {

        $now = new \DateTime("4 days ago");

        $team_member = new TeamMember();
        $team_member->setUser(
            UserHandler::getUserByUserId(1)
        );
        $team_member->setPosition(
            TeamMemberPositionHandler::getAdminPosition()
        );
        $team_member->setJoinDate($now);
        $team_member->setIsActive(true);

        $new_team = new Team();
        $new_team->setName("Test Cup Team " . StringFormatterUtils::getRandomString(10));
        $new_team->setTag(StringFormatterUtils::getRandomString(10));
        $new_team->setCreationDate($now);
        $new_team->setHomepage("https://gaming.myrisk-ev.de");
        $new_team->setLogotype("logotype");
        $new_team->addMember($team_member);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("de")
        );

        $team = TeamHandler::saveTeam($new_team);

        $this->assertGreaterThan(0, $team->getTeamId(), "Team is saved correctly.");

        $team_participant = new TeamParticipant();
        $team_participant->setTeam($team);

        ParticipantHandler::joinCup(self::$cup_5on5, $team_participant);

        $cup = CupHandler::getCupByCupId(self::$cup_5on5->getCupId());

        $participants = $cup->getCupParticipants();

        $this->assertEquals(1, count($participants), "Team is a participant.");

        $saved_team_participant_01 = $participants[0];

        $this->assertGreaterThan(0, $saved_team_participant_01->getParticipantId(), "Participant ID is set.");
        $this->assertEquals($team->getName(), $saved_team_participant_01->getTeam()->getName(), "Team name is expected.");
        $this->assertFalse($saved_team_participant_01->getCheckedIn(), "Team is not checked in yet 01.");
        $this->assertNull($saved_team_participant_01->getCheckInDateTime(), "Team is not checked in yet 02.");

        ParticipantHandler::confirmCupParticipation(self::$cup_5on5, $saved_team_participant_01);

        $cup = CupHandler::getCupByCupId(self::$cup_5on5->getCupId());

        $participants = $cup->getCupParticipants();

        $this->assertEquals(1, count($participants), "User is a participant.");

        $saved_team_participant_02 = $participants[0];

        $this->assertEquals($saved_team_participant_01->getParticipantId(), $saved_team_participant_02->getParticipantId(), "Participant ID is set.");
        $this->assertEquals($team->getName(), $saved_team_participant_02->getTeam()->getName(), "Team name is expected.");
        $this->assertTrue($saved_team_participant_02->getCheckedIn(), "Team is checked in 01.");
        $this->assertNotNull($saved_team_participant_02->getCheckInDateTime(), "Team is checked in 02.");

        ParticipantHandler::leaveCup(self::$cup_5on5, $saved_team_participant_02);

        $cup = CupHandler::getCupByCupId(self::$cup_5on5->getCupId());

        $participants = $cup->getCupParticipants();

        $this->assertEquals(0, count($participants), "Team is not a participant anymore.");

    }

}
