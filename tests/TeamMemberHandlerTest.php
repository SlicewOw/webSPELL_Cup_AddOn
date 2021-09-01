<?php declare(strict_types=1);

use myrisk\Cup\Enum\TeamEnums;
use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Team;
use \myrisk\Cup\TeamMember;
use \myrisk\Cup\TeamMemberPosition;
use \myrisk\Cup\Handler\TeamHandler;
use \myrisk\Cup\Handler\TeamMemberHandler;
use \myrisk\Cup\Handler\TeamMemberPositionHandler;

use \webspell_ng\User;
use \webspell_ng\Handler\CountryHandler;
use \webspell_ng\Handler\UserHandler;
use \webspell_ng\Utils\StringFormatterUtils;

final class TeamMemberHandlerTest extends TestCase
{

    /**
     * @var User $user
     */
    private static $user;

    /**
     * @var User $new_user
     */
    private static $new_user;

    /**
     * @var Team $team
     */
    private static $team;

    /**
     * @var TeamMemberPosition $player_position
     */
    private static $player_position;

    /**
     * @var \DateTime $creation_date
     */
    private static $creation_date;

    public static function setUpBeforeClass(): void
    {

        self::$user = UserHandler::getUserByUserId(1);

        $tmp_new_user = new User();
        $tmp_new_user->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $tmp_new_user->setPassword(
            StringFormatterUtils::generateHashedPassword(
                StringFormatterUtils::getRandomString(20)
            )
        );
        $tmp_new_user->setFirstname("Test User " . StringFormatterUtils::getRandomString(10));
        $tmp_new_user->setEmail(StringFormatterUtils::getRandomString(10) . "@webspell-ng.de");
        $tmp_new_user->setTown("Berlin");
        $tmp_new_user->setBirthday(
            new \DateTime(rand(1992, 2020) . "-" . rand(1, 12) . "-" . rand(1, 28) . " 00:00:00")
        );
        $tmp_new_user->setCountry(
            CountryHandler::getCountryByCountryShortcut("de")
        );

        self::$new_user = UserHandler::saveUser($tmp_new_user);

        self::$creation_date = new \DateTime(rand(1990, 2036) . "-01-05 12:34:56");

        $team_name = "Test Cup Team " . StringFormatterUtils::getRandomString(10);
        $team_tag = StringFormatterUtils::getRandomString(10);

        self::$player_position = TeamMemberPositionHandler::getPlayerPosition();

        $team_member = new TeamMember();
        $team_member->setUser(self::$user);
        $team_member->setJoinDate(self::$creation_date);
        $team_member->setIsActive(true);
        $team_member->setPosition(
            TeamMemberPositionHandler::getAdminPosition()
        );

        $new_team = new Team();
        $new_team->setName($team_name);
        $new_team->setTag($team_tag);
        $new_team->setCreationDate(self::$creation_date);
        $new_team->setHomepage("https://slicewuff.de");
        $new_team->setLogotype("logotype");
        $new_team->setIsDeleted(false);
        $new_team->addMember($team_member);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("de")
        );

        self::$team = TeamHandler::saveTeam($new_team);

    }

    public function testIfMemberCanBeUpdated(): void
    {

        $position = TeamMemberPositionHandler::getCoachPosition();

        $team_admin = TeamMemberHandler::getMemberByUserIdAndTeam(self::$user->getUserId(), self::$team);
        $team_admin->setPosition($position);

        TeamMemberHandler::saveSingleTeamMember(self::$team, $team_admin);

        $updated_team = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        $this->assertNull($updated_team->getTeamAdmin(), "No team admin anymore.");
        $this->assertEquals(1, count($updated_team->getMembers()), "No member anymore.");

        $team_coach = $updated_team->getMembers()[0];
        $this->assertGreaterThan(0, $team_coach->getPosition()->getPositionId(), "Position ID is set.");
        $this->assertEquals(TeamEnums::TEAM_MEMBER_POSITION_COACH, $team_coach->getPosition()->getPosition(), "Coach is set.");

    }

    public function testIfMemberCanLeaveTeam(): void
    {

        $team_member = new TeamMember();
        $team_member->setUser(self::$new_user);
        $team_member->setPosition(self::$player_position);
        $team_member->setJoinDate(
            new \DateTime("10 minutes ago")
        );

        TeamMemberHandler::joinTeam(self::$team, $team_member);

        $saved_team_member = TeamMemberHandler::getMemberByUserIdAndTeam(self::$new_user->getUserId(), self::$team);

        $this->assertEquals(self::$new_user->getUserId(), $saved_team_member->getUser()->getUserId(), "User ID is set.");
        $this->assertTrue($saved_team_member->isActive(), "Member is active.");
        $this->assertNull($saved_team_member->getLeftDate(), "Left date is not set.");

        $team_before_member_left = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        TeamMemberHandler::leaveTeam(self::$team, $saved_team_member);

        $team_after_member_left = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        $this->assertLessThan(count($team_before_member_left->getMembers()), count($team_after_member_left->getMembers()), "Member left the team successfully.");

    }

    public function testIfMemberCanBeKicked(): void
    {

        $team_member = new TeamMember();
        $team_member->setUser(self::$new_user);
        $team_member->setPosition(self::$player_position);

        TeamMemberHandler::joinTeam(self::$team, $team_member);

        $saved_team_member = TeamMemberHandler::getMemberByUserIdAndTeam(self::$new_user->getUserId(), self::$team);

        $this->assertEquals(self::$new_user->getUserId(), $saved_team_member->getUser()->getUserId(), "User ID is set.");
        $this->assertTrue($saved_team_member->isActive(), "Member is active.");
        $this->assertNull($saved_team_member->getLeftDate(), "Left date is not set.");

        $team_before_member_left = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        TeamMemberHandler::kickMember(self::$team, $saved_team_member);

        $team_after_member_left = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        $this->assertLessThan(count($team_before_member_left->getMembers()), count($team_after_member_left->getMembers()), "Member kicked successfully.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfTeamMemberIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        TeamMemberHandler::getMemberByUserIdAndTeam(-1, new Team());

    }

    public function testIfUnexpectedValueExceptionIsThrownIfTeamMemberIdDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        TeamMemberHandler::getMemberByUserIdAndTeam(999999999, self::$team);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfUserIdOfMemberIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $team_member = new TeamMember();
        $team_member->setUser(new User());

        $team = new Team();
        $team->addMember($team_member);

        TeamMemberHandler::saveTeamMembers($team);

    }

}
